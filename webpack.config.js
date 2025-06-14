const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
    ...defaultConfig,
    entry: {
        'contributor-highlights-blocks': './src/blocks/contributor-highlights/index.js',
    },
}; 