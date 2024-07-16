import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'master/customerlevel/search', prm)
}

export async function save(prm) {
    return ajaxPost(URL + 'master/customerlevel/save', prm)
}

export async function del(prm) {
    return ajaxPost(URL + 'master/customerlevel/del', prm)
}

export async function restore(prm) {
    return ajaxPost(URL + 'master/customerlevel/restore', prm)
}

// export async function search_unit(prm) {
//     return ajaxPost(URL + 'master/unit/search', prm)
// }

// export async function search_level_price(prm) {
//     return ajaxPost(URL + 'master/price/search_by_level', prm)
// }