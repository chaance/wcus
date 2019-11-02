import fs from 'fs';
import path from 'path';
import { assets } from './index';

/**
 * Scan a directory for its direct files or sub-directories.
 * @param {string} dir - Directory to search
 * @param {string} which - Which sub-items to return. Either `files` or `directories`
 * @returns {string[]} List of files or directories
 */
export function getFilesOrDirectories( dir, which = 'files' ) {
	return fs.readdirSync( dir ).filter( function( file ) {
		const isDirectory = fs.statSync( path.join( dir, file ) ).isDirectory();
		return which === 'directories' ? isDirectory : ! isDirectory;
	} );
}

/**
 * Scan a directory for its direct files.
 * @param {string} dir - Directory to search
 * @returns {string[]} List of files
 */
export function getFiles( dir ) {
	return getFilesOrDirectories( dir );
}

/**
 * Scan a directory for its direct directories.
 * @param {string} dir - Directory to search
 * @returns {string[]} List of directories
 */
export function getDirectories( dir ) {
	return getFilesOrDirectories( dir, 'directories' );
}

/**
 * List of source files for gulp to access asset files.
 * @param {string} assetType - Either `scss` or `js`
 * @returns {string[]} List of files
 */
export function getAssetSrc( assetType ) {
	if ( ! [ 'scss', 'js' ].includes( assetType ) ) {
		throw new Error(
			`Asset type must be either 'scss' or 'js'; ${ assetType } detected.`,
		);
	}

	const assetPath = `${ assets }/${ assetType }`;

	// Get top-level directories. Ignored directories must begin with an underscore.
	const directories = getDirectories( assetPath, 'directories' ).filter(
		dirName => ! dirName.startsWith( '_' ),
	);

	const src = directories.reduce( ( prev, directory ) => {
		const allFiles = getFiles( `${ assets }/${ assetType }/${ directory }` )
			.filter(
				filename =>
					! filename.startsWith( '_' ) ||
					path.extname( filename ) === `.${ assetType }`,
			)
			.map(
				filename => `${ assets }/${ assetType }/${ directory }/${ filename }`,
			);
		return [ ...prev, ...allFiles ];
	}, [] );
	return src;
}
