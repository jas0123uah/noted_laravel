import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
function getAccessTokenFromCookie() {
    // const cookies = document.cookie.split(";");
    // console.log(cookies, "COOKIES")
    // for (let i = 0; i < cookies.length; i++) {
    //     const cookie = cookies[i].trim();
    //     if (cookie.startsWith("access_token=")) {
    //         return cookie.substring("access_token=".length);
    //     }
    // }
    // return null;

    return localStorage.getItem("access_token");

    // if (!accessToken) {
    //     console.log(document.cookie, "COOKIE");
    //     const cookieValue = document.cookie
    //         .split("; ")
    //         .find((row) => row.startsWith("access_token="))
    //         ?.split("=")[1];

    //     if (cookieValue) {
    //         localStorage.setItem("access_token", cookieValue);
    //     }
    // }
    // return localStorage.getItem("access_token");
}
import axios from 'axios';
axios.defaults.withCredentials = true;

axios.interceptors.request.use(function(config){
        const access_token = getAccessTokenFromCookie();
        console.log(access_token, "ACCESSS TOKEN")
        config.headers.Authorization = `Bearer ${access_token}`;

        return config;


})
// Retrieve the access token from storage
//const access_token = localStorage.getItem('access_token');
window.axios = axios;


window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
//window.axios.defaults.headers.common['Authorization'] = `Bearer ${access_token}`
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
