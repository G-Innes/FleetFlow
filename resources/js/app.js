import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { createApp, h } from "vue";
import ziggyRoute from "ziggy";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue");
        const importer = pages[`./Pages/${name}.vue`];
        if (!importer) {
            throw new Error(`Inertia page not found: ${name}`);
        }
        return importer();
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .provide("route", ziggyRoute)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
