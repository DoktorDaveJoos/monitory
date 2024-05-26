/** @type { import('@storybook/vue3').Preview } */
import '../resources/css/app.css';

const preview = {
    parameters: {
        controls: {
            matchers: {
                color: /(background|color)$/i,
                date: /Date$/i,
            },
        },
        layout: 'centered',
        backgrounds: {
            default: 'light',
            values: [
                {
                    name: 'light',
                    value: '#f5f5f5',
                },
                {
                    name: 'semi-dark',
                    value: '#1F2937',
                },
                {
                    name: 'dark',
                    value: '#000000',
                },
            ],
        },
    },
};

export default preview;
