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
                    'on-primary': '#f4ecec',

                    secondary: '#5a5a5f',
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

                    success: '#2e7d32',
                    'on-success': '#ffffff',
                    warning: '#b26a00',
                    'on-warning': '#ffffff',
                    error: '#c62828',
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
