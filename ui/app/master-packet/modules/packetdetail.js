// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_packet.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',

        items: [],
        total_item: 0,
        selected_item: {},
        headers_price: [],
        dialog_price: false,
        prices: [],
        fees: [],

        items_av: [],
        total_item_av: 0,
        total_item_av_page: 1,
        selected_item_av: {},
        dialog_item_av: false,

        current_page: 1,
        selected_tab: 1,

        sdate: '',
        edate: ''
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

        update_items(state, data) {
            state.items = data
        },

        update_prices(state, data) {
            state.prices = data
        },

        set_fees(state, data) {
            state.fees = data
        },

        set_selected_item(state, val) {
            state.selected_item = val
        },

        set_headers_price(state, data) {
            state.headers_price = data
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_item(state, val) {
            state.total_item = val
        },

        update_current_page(state, val) {
            state.current_page = val
        },

        update_items_av(state, data) {
            state.items_av = data
        },

        set_selected_item_av(state, val) {
            state.selected_item_av = val
        },

        update_total_item_av(state, val) {
            state.total_item_av = val
        }
    },
    actions: {
        async search(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.packet_id = context.rootState.packet.selected_packet.M_PacketID
                let resp = await api.search_detail(prm)

                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total,
                        fees: resp.data.fees
                    }

                    context.commit("update_items", data.records)
                    context.commit("update_total_item", data.total)
                    context.commit("set_fees", data.fees)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_av_item(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.packet_id = context.rootState.packet.selected_packet.M_PacketID
                prm.page = context.state.current_page
                let resp = await api.search_av_item(prm)

                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total,
                        total_page: resp.data.total_page
                    }

                    context.commit("update_items_av", data.records)
                    context.commit("update_total_item_av", data.total)
                    context.commit("set_common", ['total_item_av_page', data.total_page])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async add(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.packet_id = context.rootState.packet.selected_packet.M_PacketID
                prm.item_id = context.state.selected_item_av.M_ItemID
                let resp = await api.add_detail(prm)

                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    // context.commit("set_common", ['dialog_item_av', false])
                    context.dispatch("search", {})
                    context.dispatch("search_av_item", {})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async del(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.id = context.state.selected_item.M_PacketDetailID

                let resp = await api.del_detail(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.dispatch('search', {})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_header_price(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                let resp = await api.search_header_price(prm)

                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_headers_price", resp.data.header)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_detail_price(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.packet_id = context.rootState.packet.selected_packet.M_PacketID
                prm.item_id = context.state.selected_item.M_ItemID
                let resp = await api.search_detail_price(prm)

                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit('update_prices', resp.data)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async save_price(context, prm) {
            try {
                let price = context.state.selected_item.prices
                let price_n = context.state.selected_item.prices_n
                let jdata = []
                for (let p in price) {
                    jdata.push({ price_normal: price_n[p], price_sale: price[p], price_level_code: p })
                }

                prm.token = one_token();
                prm.jdata = JSON.stringify(jdata)
                prm.item_id = context.state.selected_item.M_ItemID
                prm.packet_id = context.rootState.packet.selected_packet.M_PacketID

                let resp = await api.save_price(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    // context.commit('update_prices', resp.data)
                    context.commit('set_snackbar', true, { root: true })
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async save_fee(context) {
            try {
                let prm = {}
                let fees = context.state.fees
                let jdata = []
                for (let f of fees) {
                    jdata.push({
                        fee_level_code: f.M_CustomerLevelCode,
                        fee_amount: f.M_PacketFeeAmount,
                        fee_reward_amount: f.M_PacketFeeRewardAmount,
                        fee_point_amount: f.M_PacketFeePointAmount
                    })
                }

                prm.token = one_token();
                prm.jdata = JSON.stringify(jdata)
                prm.packet_id = context.rootState.packet.selected_packet.M_PacketID

                let resp = await api.save_fee(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    // context.commit('update_prices', resp.data)
                    context.commit('set_snackbar', true, { root: true })
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}