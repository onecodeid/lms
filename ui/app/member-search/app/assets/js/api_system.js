import { URL, ajaxPost } from "./global.js"

export async function search_menu_group(prm) {
    return ajaxPost(URL + 'systm/menu/search_group', prm)
}