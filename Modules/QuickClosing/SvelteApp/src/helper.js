

export function isLocal(){
    if(location.hostname == 'localhost'){
        return true
    } else {
        return false
    }

}

export async function remoteRequest(url, args = {}){


    if(args.headers === undefined){
        args.headers = {}
    }

    if(isLocal()){
        args.headers["X-CSRF-TOKEN"] = "ddd"
    } else {
        args.headers["X-CSRF-TOKEN"] = getCsrfToken()
    }

    const response = await fetch(url, args)
    return await response;
}

export function dd(data){
    if(isLocal()){
        console.log(data);
    }
}