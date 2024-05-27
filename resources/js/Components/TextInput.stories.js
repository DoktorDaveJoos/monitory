import TextInput from './TextInput.vue';
import { ref } from 'vue';

//👇 This default export determines where your story goes in the story list
export default {
    component: TextInput,
};

/*
 *👇 Render functions are a framework specific feature to allow you control on how the component renders.
 * See https://storybook.js.org/docs/api/csf
 * to learn how to use render functions.
 */
export const Basic = {
    render: (args) => ({
        components: { TextInput },
        setup() {
            const value = ref('');

            return { args, value };
        },
        template: '<TextInput  v-model="value"/>',
    }),
    args: {
        //👇 The args you need here will depend on your component
    },
};
