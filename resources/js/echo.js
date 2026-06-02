import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

export function initEcho() {
    window.Pusher = Pusher;

    return new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        wssPort: import.meta.env.VITE_REVERB_PORT,
        forceTLS: false,
        enabledTransports: ['ws'],
        authEndpoint: '/broadcasting/auth',
        withCredentials: true,
        auth: {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
        },
    });
}
