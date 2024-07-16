// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_coupon.js"
import { URL, one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        query: '',

        items: [],
        expedition_id: 0,

        coupon_code: '',
        coupon_type: '',
        coupon_amount: 0,
        coupon_amounts: 0,
        coupon_id: 0,
        coupon_item_id: 0,
        coupon_courier_id: 0,
        coupon_set: false,
        coupon_note: '',
        coupon_error: false,
        coupon_error_msg: ''
    },
    mutations: {
        set_common(state, v) {
            let name = v[0]
            let val = v[1]
            if (typeof(val) == "string")
                eval(`state.${name} = "${val}"`)
            else
                eval(`state.${name} = ${val}`)
        },

        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search(state, search) {
            state.search = search
        },

        set_coupon_item_id(state, val) {
            state.coupon_item_id = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        set_items (state, val) {
            state.items = val
        },

        clear_coupon (state) {
            state.coupon_code = '',
            state.coupon_type = '',
            state.coupon_amount = 0,
            state.coupon_amounts = 0,
            state.coupon_id = 0,
            state.coupon_item_id = 0,
            state.coupon_courier_id = 0,
            state.coupon_set = false,
            state.coupon_note = '',
            state.coupon_error = false,
            state.coupon_error_msg = ''
        }
    },
    actions: {        
        async check_coupon(context) {
            context.commit("update_search_status", 1)
            try {
                let x = []
                let y = []
                for (let i of context.state.items)
                    if (i.is_packet == "N")
                        x.push(i.item_id)
                    else
                        y.push(i.item_id)
                let prm = {
                    token: one_token(),
                    coupon_code: context.state.coupon_code,
                    courier_id: context.state.expedition_id,
                    items: x,
                    packets: y
                }

                let resp = await api.check_coupon(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                    context.commit('set_common', ['coupon_error', true])
                    context.commit('set_common', ['coupon_error_msg', resp.message])
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    let x = resp.data
                    context.commit('set_common', ['coupon_code', x.coupon_code])
                    context.commit('set_common', ['coupon_amount', x.coupon_amount])
                    context.commit('set_common', ['coupon_type', x.coupon_type])
                    context.commit('set_common', ['coupon_id', x.coupon_id])
                    context.commit('set_common', ['coupon_courier_id', x.coupon_courier_id])
                    context.commit('set_coupon_item_id', x.coupon_item_id)
                    context.commit('set_common', ['coupon_set', true])
                    context.commit('set_common', ['coupon_error', false])
                    context.commit('set_common', ['coupon_error_msg', ''])
                    
                    let coupon_amounts = 0
                    let coupon_note = ''
                    if (x.coupon_type == 'COUPON.PRODUCT' || x.coupon_type == 'COUPON.CATEGORY') {
                        let it = []
                        for (let i of context.state.items) {
                            if (x.coupon_item_id.indexOf(parseInt(i.item_id)) > -1 && i.is_packet == 'N') {
                                i.coupon_amount = x.coupon_amount
                                // i.item_subtotal = i.item_subtotal - (i.item_qty*x.coupon_amount)
                                coupon_amounts += (i.item_qty*x.coupon_amount)
                                coupon_note += (coupon_note == '' ? i.item_name : ', '+i.item_name)
                                if (i.item_subtotal < 0 ) {
                                    coupon_amounts += 0 - i.item_subtotal
                                    i.item_subtotal = 0
                                }
                            }
                            it.push(i)
                        }
                        context.commit('set_items', it)
                        context.commit('set_common', ['coupon_note', 'Kupon Produk : '+coupon_note])
                    } else if (x.coupon_type == 'COUPON.PACKET') {
                        let it = []
                        for (let i of context.state.items) {
                            if (x.coupon_item_id.indexOf(parseInt(i.item_id)) > -1 && i.is_packet == 'Y') {
                                i.coupon_amount = x.coupon_amount
                                // i.item_subtotal = i.item_subtotal - (i.item_qty*x.coupon_amount)
                                coupon_amounts += (i.item_qty*x.coupon_amount)
                                coupon_note += (coupon_note == '' ? i.item_name : ', '+i.item_name)
                                if (i.item_subtotal < 0 ) {
                                    coupon_amounts += 0 - i.item_subtotal
                                    i.item_subtotal = 0
                                }
                            }
                            it.push(i)
                        }
                        context.commit('set_items', it)
                        context.commit('set_common', ['coupon_note', 'Kupon Produk : '+coupon_note])
                    } else if (x.coupon_type == 'COUPON.COURIER') {
                        coupon_amounts = x.coupon_amount > this.courier_cost ? this.courier_cost : x.coupon_amount
                        // context.commit('set_common', ['coupon_amounts', coupon_amounts])
                        context.commit('set_common', ['coupon_note', 'Kupon Produk : '+coupon_note])
                    } else {
                        coupon_amounts = x.coupon_amount
                    }
                    context.commit('set_common', ['coupon_amounts', coupon_amounts])

                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}