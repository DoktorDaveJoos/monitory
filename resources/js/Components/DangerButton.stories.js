import DangerButton from './DangerButton.vue';
import { Trash2 } from 'lucide-vue-next';

//👇 This default export determines where your story goes in the story list
export default {
    component: DangerButton,
};

/*
 *👇 Render functions are a framework specific feature to allow you control on how the component renders.
 * See https://storybook.js.org/docs/api/csf
 * to learn how to use render functions.
 */
export const Basic = {
    render: (args) => ({
        components: { DangerButton },
        setup() {
            return { args };
        },
        template: '<DangerButton>' + 'delete' + '</DangerButton>',
    }),
    args: {
        //👇 The args you need here will depend on your component
    },
};

export const WithIcon = {
    render: (args) => ({
        components: { DangerButton, Trash2 },
        setup() {
            return { args };
        },
        template:
            '<DangerButton>' +
            'Delete' +
            '<template #icon><Trash2 class="ml-1 h-4 w-4" /></template>' +
            '</DangerButton>',
    }),
    args: {
        //👇 The args you need here will depend on your component
    },
};
