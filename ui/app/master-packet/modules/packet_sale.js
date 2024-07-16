// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_packet.js"
import { one_token } from "../../assets/js/global.js"
import { save } from "../../master-item/modules/api_item.js"

export default {
    namespaced: true,
    state: {
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',

        prices: [],
        sdate: "",
        edate: "",

        trigger_date: false
    },
    mutations: {
        set_common (state, v) {
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

        set_prices(state, v) {
            state.prices = v
        }
    },
    actions: {
        async search(context) {
            try {
                let prm = {token : one_token(), 
                            packet_id: context.rootState.packet.selected_packet.M_PacketID
                        }

                let resp = await api.search_sale(prm)
                if (resp.status != "OK") {
                    context.commit("set_common", ["search_status", 3])
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_prices", resp.data.records)
                    context.commit('set_common', ['trigger_date', false])
                    let sdate = new Date().toISOString().substr(0, 10)
                    let edate = new Date().toISOString().substr(0, 10)
                    for (let p of resp.data.records) {
                        if (p.M_PacketSaleStartDate != null)
                            sdate = p.M_PacketSaleStartDate
                        if (p.M_PacketSaleEndDate != null)
                            edate = p.M_PacketSaleEndDate
                    }
                    context.commit('set_common', ['edate', edate])
                    context.commit('set_common', ['sdate', sdate])
                    setTimeout(function() {
                        context.commit('set_common', ['trigger_date', true])
                    }, 500)
                    
                }
            } catch (e) {
                context.commit("set_common", ["search_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async save(context) {
            try {
                let jdata = []
                for (let p of context.state.prices) {
                    jdata.push({price_amount:p.M_PacketSaleAmount,
                            price_level_id:p.M_CustomerLevelID,
                            price_sdate:context.state.sdate,
                            price_edate:context.state.edate})
                }

                let prm = {token : one_token(), 
                            packet_id: context.rootState.packet.selected_packet.M_PacketID,
                            jdata: JSON.stringify(jdata)
                        }

                let resp = await api.save_promo(prm)
                if (resp.status != "OK") {
                    context.commit("set_common", ["search_status", 3])
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    console.log(resp)
                    context.commit('set_snackbar', true, {root:true})
                }
            } catch (e) {
                context.commit("set_common", ["search_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}