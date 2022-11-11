const defaultConfig = require("@wordpress/scripts/config/webpack.config");
module.exports = {
	...defaultConfig,
	entry: {
		'customizer': './inc/customizer/react/src/index.js', // 'name' : 'path/file.ext'.
		'meta': './inc/meta/react/src/index.js', // 'name' : 'path/file.ext'.
		'dashboard': './inc/dashboard/react/src/index.js', // 'name' : 'path/file.ext'.
	},
	output: {
		filename: '[name].js',
		path: __dirname + '/assets/js/admin'
	},
};