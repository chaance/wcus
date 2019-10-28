const path = require( 'path' );
const baseDir = path.resolve( __dirname, '../' );
const assets = path.resolve( baseDir, 'assets' );
const dist = path.resolve( baseDir, 'dist' );
const env = process.env.NODE_ENV;
const isDev = env === 'development';
const isProd = env === 'production';
const isStaging = env === 'staging';

const settings = {
	theme: {
		slug: 'wcus-demo',
	},
};

/**
 * A theme configuration object.
 * @typedef {Object} ThemeConfig
 * @property {ThemeSettings} theme - Theme settings
 * @property {string} baseDir - The theme's base directory
 * @property {string} assets - The theme's source directory for assest
 * @property {string} dist - The theme's output directory for assets
 * @property {string} env - The value of `process.env.NODE_ENV`
 * @property {boolean} isDev - Whether or not the current environment is `development`
 * @property {boolean} isProd - Whether or not the current environment is `production`
 * @property {boolean} isStaging - Whether or not the current environment is `staging`
 * @property {(task: string) => string} successMessage - Generates a success message when a task is complete
 */

/** @type {ThemeConfig} */
const config = {
	...settings,
	baseDir,
	assets,
	dist,
	env,
	isDev,
	isProd,
	isStaging,
	successMessage: task => `TASK: "${ task }" Completed! 🍻`,
};

module.exports = config;
