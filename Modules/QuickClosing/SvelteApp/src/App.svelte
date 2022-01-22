<script>
	import { onMount } from 'svelte';
	import { isLocal, dd, remoteRequest } from './helper.js';

    let baseUrl =  new URL('https://hd.bizio.site/conversation/ajax');
    let context = {
        isLocal: true,
        conversation_id: 8791,
        folder_id: 37,
    }
    
    $: {
        dd(context)
    }

    onMount(() => {
        if( ! isLocal() ){
            setContext()
        }
    })

    function setContext(){
        context = {
            isLocal: false,
            conversation_id: getGlobalAttr('conversation_id'),
            folder_id: getQueryParam('folder_id'),
        }
    }


    function handleClick() {

        let data = new FormData();
        data.append('action', 'conversation_change_status');
        data.append('status', 3);
        data.append('conversation_id', context.conversation_id);
        data.append('folder_id', context.folder_id);


        let args = {
            method: 'POST',
            body: data,
        }


        baseUrl.searchParams.append('folder_id', context.folder_id);

        let url = baseUrl.toString();

        
        dd(args)
        dd(url)


        // return;

        remoteRequest( url, args ).then((data) => {
				console.log(data);
        })
    }
</script>

<section class="app-toolbox-wrapper">
    <div class="conv-close-action">
        <button type="button" on:click={handleClick}>
            <i class="glyphicon glyphicon-ok-circle"></i>
        </button>
    </div>
</section>

<style>
    .app-toolbox-wrapper {
        margin-top: 3rem;

    }

    .conv-close-action {
        box-shadow: 0px 0px 1rem #0000002b;
        border-radius: 0.3rem;

    }

    button {
        display: inline-block;
        line-height: 0;
        cursor: pointer;
        border: none;
        padding: 1rem;
        font-size: 1rem;
        border-radius: 0.25rem;
        margin: 0rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out,
            border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

</style>
