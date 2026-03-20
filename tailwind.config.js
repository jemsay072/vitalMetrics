import flowbite from 'flowbite/plugin';

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.jsx",
        "./node_modules/flowbite/**/*.js"
    ],
    theme: {
        extend: {},
    },
    plugins: [
        flowbite
    ]
}