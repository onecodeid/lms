import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'master/campaign/search', prm)
}

export async function save(prm) {
    return ajaxPost(URL + 'master/campaign/save', prm)
}

export async function del(prm) {
    return ajaxPost(URL + 'master/campaign/del', prm)
}

export async function search_expedition(prm) {
    return ajaxPost(URL + 'master/expedition/search', prm)
}

export async function search_service(prm) {
    return ajaxPost(URL + 'master/expedition/search_service_kecamatan', prm)
}

// DETAIL
export async function search_detail(prm) {
    return ajaxPost(URL + 'master/campaigndetail/search', prm)
}


// export async function search_av_item(prm) {
//     return ajaxPost(URL + 'master/campaigndetail/search_av_item', prm)
// }

export async function add_detail(prm) {
    return ajaxPost(URL + 'master/campaigndetail/add', prm)
}

export async function del_detail(prm) {
    return ajaxPost(URL + 'master/campaigndetail/del', prm)
}
