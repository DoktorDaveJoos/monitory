import { cva, type VariantProps } from 'class-variance-authority';

export { default as Button } from './Button.vue';

export const buttonVariants = cva(
    'inline-flex uppercase font-bold tracking-widest items-center justify-center whitespace-nowrap rounded-lg text-sm ring-offset-background transition-colors focus-visible:outline-none focus:ring-2 focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50',
    {
        variants: {
            variant: {
                default:
                    'bg-primary text-primary-foreground hover:bg-primary/90 focus:ring-primary',
                destructive:
                    'bg-destructive text-destructive-foreground hover:bg-destructive/90 focus:ring-destructive',
                outline:
                    'border border-input bg-background hover:bg-accent hover:text-accent-foreground focus:ring-accent',
                secondary:
                    'bg-secondary text-secondary-foreground hover:bg-secondary/80 focus:ring-secondary',
                ghost: 'hover:bg-accent hover:text-accent-foreground focus:ring-0',
                link: 'text-primary underline-offset-4 hover:underline focus:ring-0',
            },
            size: {
                default: 'h-10 px-4 py-2',
                xs: 'h-7 rounded px-2',
                sm: 'h-9 rounded-md px-3',
                lg: 'h-11 rounded-md px-8',
                icon: 'h-10 w-10',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
);

export type ButtonVariants = VariantProps<typeof buttonVariants>;
