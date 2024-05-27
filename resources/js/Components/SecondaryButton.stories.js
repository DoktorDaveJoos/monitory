import SecondaryButton from './SecondaryButton.vue';
import { ChevronDown } from 'lucide-vue-next';

//👇 This default export determines where your story goes in the story list
export default {
    component: SecondaryButton,
};

/*
 *👇 Render functions are a framework specific feature to allow you control on how the component renders.
 * See https://storybook.js.org/docs/api/csf
 * to learn how to use render functions.
 */
export const Basic = {
    render: (args) => ({
        components: { SecondaryButton },
        setup() {
            return { args };
        },
        template: '<SecondaryButton>' + 'go back' + '</SecondaryButton>',
    }),
    args: {
        //👇 The args you need here will depend on your component
    },
};

export const WithIcon = {
    render: (args) => ({
        components: { SecondaryButton, ChevronDown },
        setup() {
            return { args };
        },
        template:
            '<SecondaryButton>' +
            'see more' +
            '<template #icon><ChevronDown class="ml-1 h-4 w-4" /></template>' +
            '</SecondaryButton>',
    }),
    args: {
        //👇 The args you need here will depend on your component
    },
};
