<script>
    export let ajaxUrl = "#";
    let href = "https://hd.bizio.site/";
	let count = 0;

	function closeConversation() {
		count += 1;
		alert(count);
	}
</script>

<main>
    <div class="conv-close-action">
        <button type="button" on:click={closeConversation}>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                fill="currentColor"
                class="bi bi-check-square"
                viewBox="0 0 16 16"
            >
                <path
                    d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"
                />
                <path
                    d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.235.235 0 0 1 .02-.022z"
                />
            </svg>
        </button>
    </div>
    <p>test app: <a {href} target="_blank">{href}</a></p>
    <p>ajax url: {ajaxUrl}</p>
</main>

<style>
    button {
        display: inline-block;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        text-align: center;
        text-decoration: none;
        vertical-align: middle;
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
        cursor: pointer;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        border-radius: 0.25rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out,
            border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    main {
        text-align: center;
        padding: 1em;
        max-width: 240px;
        margin: 0 auto;
    }

  

    @media (min-width: 640px) {
        main {
            max-width: none;
        }
    }
</style>
