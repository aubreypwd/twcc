<?php

use Composer\Semver\Comparator;

$autoload = dirname( __FILE__ ) . '/vendor/autoload.php';

if ( ! is_readable( $autoload ) ) {
	$autoload = dirname( __FILE__ ) . '/../../../vendor/autoload.php';

	if ( ! is_readable( $autoload ) ) {
		echo "Please run composer global dumpautoload";
		exit;
	}
}

require_once $autoload;

$subcommand = $argv[1] ?? '';

$subcommands = [
	'examine',
	'commands',
	'update',
];


if ( ! in_array( $subcommand, $subcommands ) ) {
	echo "Please use: twcc " . implode( '|', $subcommands ) . " [left file] [middle file] [right file]\n";
	exit;
}

$files = [
	'left'   => $argv[2] ?? '',
	'middle' => $argv[3] ?? '',
	'right'  => $argv[4] ?? '',
];

foreach ( $files as $file ) {
	if ( ! file_exists( $file ) ) {
		echo "The file '{$file}' does not exist.\n";
		exit;
	}
}

$json_files = [
	'left'   => json_decode( file_get_contents( $files['left'] ), JSON_OBJECT_AS_ARRAY ),
	'middle' => json_decode( file_get_contents( $files['middle'] ), JSON_OBJECT_AS_ARRAY ),
	'right'  => json_decode( file_get_contents( $files['right'] ), JSON_OBJECT_AS_ARRAY ),
];

if ( ! is_array( $json_files['middle'] ) ) {
	echo "The {$files[$key]} does not appear to be JSON.\n";
}

foreach ( [ 'require', 'require-dev'] as $require ) {
	if ( ! isset( $json_files['middle'][ $require ] ) ) {
		continue;
	}

	foreach ( $json_files['middle'][ $require ] as $package => $middle_version ) {
		$left_version = $json_files['left'][ $require ][ $package ] ?? 'none';
		$right_version = $json_files['right'][ $require ][ $package ] ?? 'none';

		$middle_version_orig = $middle_version;

		$version = '';

		$updated = '';

		if ( Comparator::greaterThan( $left_version, $middle_version ) ) {
			$middle_version = $left_version;
			$updated = $files['left'];
		}

		if ( Comparator::greaterThan( $right_version, $middle_version ) ) {
			$middle_version = $right_version;
			$updated = $files['right'];
		}

		$update = "updating {$package} from {$middle_version_orig} to {$middle_version} from {$updated}";

		if ( empty( $updated ) ) {
			continue;
		}

		$flag = 'require-dev' === $require ? '--save-dev' : '';
		$package = "{$package}:{$middle_version} {$flag}";

		if ( 'commands' === $subcommand ) {

			echo "composer update {$package}\n";
			continue;
		}

		if ( 'update' === $subcommand ) {
			$working_dir = dirname( $files['middle'] );

			shell_exec( "composer --working-dir=\"{$working_dir}\" update {$package}" );
			continue;
		}

		echo "Suggest {$update}\n";
	}
}
