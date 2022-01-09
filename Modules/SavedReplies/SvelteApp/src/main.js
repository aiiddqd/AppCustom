import App from './App.svelte';

const app = new App({
	target: document.querySelector('.app-action-close'),
	props: {
		ajaxUrl: '#'
	}
});

export default app;