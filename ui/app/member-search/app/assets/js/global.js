var URLx
if (window.location.hostname == "localhost")
    URLx = "http://localhost:8080/one-sales/api/"
else
    URLx = "http://"+window.location.hostname+"/api/"
export const URL = URLx
export const UPL_URL = "http://platform.zalfa.id/uploads/"

export function one_token () {
    let x = ''
    try {
        x = localStorage.getItem('token')
    } catch (error) {
        x = ''
    }

    return x
}

export async function ajaxPost(url, prm) {
    try {
        var resp = await axios.post(url, prm);
        if (resp.status != 200) {
            return {
                status: "ERR",
                message: resp.statusText
            };
        }
        let data = resp.data;
        return data;
    } catch (e) {
        return {
            status: "ERR",
            message: e.message
        };
    }
}

export function one_money(inp, format) {
    return numeral(inp).format(format ? format : '0,000')
}

export async function menu_group(prm) {
    return ajaxPost(URL + 'systm/menu/search_group', prm)
}

export function one_user() {
    return JSON.parse(localStorage.getItem('user'))
}
 