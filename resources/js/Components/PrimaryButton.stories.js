import PrimaryButton from './PrimaryButton.vue';
import { Plus } from 'lucide-vue-next';

//👇 This default export determines where your story goes in the story list
export default {
    component: PrimaryButton,
};

/*
 *👇 Render functions are a framework specific feature to allow you control on how the component renders.
 * See https://storybook.js.org/docs/api/csf
 * to learn how to use render functions.
 */
export const Primary = {
    render: (args) => ({
        components: { PrimaryButton, Plus },
        setup() {
            return { args };
        },
        template: '<PrimaryButton>' +
            'ADD MONITOR' +
            '<template #icon><Plus class="h-4 w-4" /></template>' +
            '</PrimaryButton>',
    }),
    args: {
        //👇 The args you need here will depend on your component
    },
};
