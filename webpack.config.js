const path = require('path');

module.exports = {
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
        },
    },
    module: {
        rules: [
            { test: /jquery-mousewheel/, use: "imports?define=>false&this=>window" },
            { test: /malihu-custom-scrollbar-plugin/, use: "imports?define=>false&this=>window" }
        ]
    }
};
