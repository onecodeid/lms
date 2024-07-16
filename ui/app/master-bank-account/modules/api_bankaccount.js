import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'master/bankaccount/search', prm)
}

export async function save(prm) {
    return ajaxPost(URL + 'master/bankaccount/save', prm)
}

export async function del(prm) {
    return ajaxPost(URL + 'master/bankaccount/del', prm)
}

export async function search_bank(prm) {
    return ajaxPost(URL + 'master/bank/search', prm)
}