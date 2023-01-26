module.exports = {
    extends: ["plugin:vue/recommended"],

    parserOptions: {
        parser: "@typescript-eslint/parser",
        requireConfigFile: false,
    },

    rules: {
        "no-plusplus": [
        "error",
        {
            allowForLoopAfterthoughts: true,
        },
        ],
        "no-param-reassign": 0,

        indent: ["error", 2],
        "vue/html-indent": [
        "error",
        4,
        {
            baseIndent: 1,
        },
        ],
        "vue/script-indent": [
        "error",
        4,
        {
            baseIndent: 1,
        },
        ],
    },

    overrides: [
    {
        files: ["*.vue"],
        rules: {
            indent: "off",
        },
    },
    ],
};
