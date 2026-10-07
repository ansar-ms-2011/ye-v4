import '@mdi/font/css/materialdesignicons.css';
import 'vuetify/styles';

import { createVuetify } from 'vuetify';
import type { Appearance } from '@/types';

const DEFAULT_APPEARANCE: Appearance = 'system';

const getStoredAppearance = (): Appearance => {
    if (typeof window === 'undefined') {
        return DEFAULT_APPEARANCE;
    }

    const value = window.localStorage.getItem('appearance');

    if (value === 'light' || value === 'dark' || value === 'system') {
        return value;
    }

    return DEFAULT_APPEARANCE;
};

export default createVuetify({
    ssr: true,
    defaults: {
        VBtn: {
            rounded: 'md',
        },
        VCard: {
            rounded: 'lg',
        },
        VTextField: {
            density: 'compact',
            variant: 'outlined',
            hideDetails: 'auto',
        },
        VTextarea: {
            density: 'compact',
            variant: 'outlined',
            hideDetails: 'auto',
        },
    },
    theme: {
        defaultTheme: getStoredAppearance(),
        themes: {
            light: {
                dark: false,
                colors: {
                    primary: '#005E96',
                    'primary-darken-1': '#014066',
                    'primary-lighten-1': '#028fe2',
                    'primary-lighten-2': '#219ae1',
                    'primary-lighten-3': '#42a6e1',
                    'primary-lighten-4': '#70b3db',
                    'primary-lighten-5': '#b7d6e8',
                    'on-primary': '#f4ecec',
                    'on-primary-hover': '#ede4e4',

                    secondary: '#5a5a5f',
                    'secondary-lighten-1': '#808086',
                    'secondary-lighten-2': '#a1a1a8',
                    'secondary-lighten-3': '#d9d9dd',
                    'secondary-darken-1': '#3a3a3c',
                    'on-secondary': '#ffffff',

                    background: '#f7f7f8',
                    surface: '#ffffff',
                    'surface-bright': '#ffffff',
                    'surface-light': '#f0f0f2',
                    'surface-variant': '#e8e8ea',
                    'on-surface': '#1c1c1e',
                    'on-surface-variant': '#5a5a5f',
                    'on-background': '#1c1c1e',

                    outline: '#c7c7cc',
                    'outline-variant': '#e0e0e3',

                    'success-darker-1': '#1e9024',
                    'success-darker-2': '#0c7d12',
                    'success-darker-3': '#036908',
                    success: '#46ab4b',
                    'success-lighten-1': '#6bd470',
                    'success-lighten-2': '#8fea93',
                    'success-lighten-3': '#a1eda4',
                    'success-lighten-4': '#c2f4c4',
                    'success-lighten-5': '#C2F4C4FF',
                    'on-success': '#ffffff',
                    warning: '#b26a00',
                    'warning-lighten-1': '#bf7a14',
                    'warning-lighten-2': '#cf8d2c',
                    'warning-lighten-3': '#d8a04d',
                    'warning-lighten-4': '#e1b473',
                    'warning-lighten-5': '#eac590',
                    'on-warning': '#ffffff',
                    'error-darker-1': '#ab1414',
                    'error-darker-2': '#a20202',
                    'error-darker-3': '#7f0101',
                    error: '#c62828',
                    'error-lighten-1': '#e64a4a',
                    'error-lighten-2': '#f06262',
                    'error-lighten-3': '#f48fb1',
                    'error-lighten-4': '#f8b3cc',
                    'error-lighten-5': '#f9c5d5',
                    'on-error': '#ffffff',
                    info: '#1565c0',
                    'on-info': '#ffffff',
                },
            },
            dark: {
                dark: true,
                colors: {
                    primary: '#e6e6e8',
                    'primary-darken-1': '#c7c7cc',
                    'primary-lighten-1': '#ffffff',
                    'on-primary': '#1c1c1e',

                    secondary: '#a0a0a5',
                    'secondary-darken-1': '#7a7a80',
                    'on-secondary': '#1c1c1e',

                    background: '#0e0e10',
                    surface: '#18181b',
                    'surface-bright': '#252528',
                    'surface-light': '#1f1f22',
                    'surface-variant': '#2c2c30',
                    'on-surface': '#e8e8ea',
                    'on-surface-variant': '#a0a0a5',
                    'on-background': '#e8e8ea',

                    outline: '#48484c',
                    'outline-variant': '#2c2c30',

                    success: '#81c784',
                    'on-success': '#0e2f10',
                    warning: '#ffb74d',
                    'on-warning': '#3a2400',
                    error: '#ef9a9a',
                    'on-error': '#3a0c0c',
                    info: '#64b5f6',
                    'on-info': '#04294d',
                },
            },
        },
    },
});
