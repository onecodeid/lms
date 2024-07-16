// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_packet.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        
        selected_packet: {},
        dialog_new: false,

        packet_name: "",
        packet_code: "",
        packet_publish: 'Y',
        packet_img: ''
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

        set_selected_packet(state, val) {
            state.selected_packet = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        set_dialog_new (state, v) {
            state.dialog_new = v
        }
    },
    actions: {
        async save(context) {
            
            try {
                let prm = {token : one_token(), 
                            packet_name: context.state.packet_name,
                            packet_code: context.state.packet_code,
                            packet_publish: context.state.packet_publish,
                            packet_img: context.state.packet_img
                        }

                if (context.state.edit)
                        prm.packet_id = context.rootState.packet.selected_packet.M_PacketID

                let resp = await api.save(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_status", 2])
                    context.commit("update_search_error_message", "")
                    // let data = {
                    //     records: resp.data.records,
                    //     total: resp.data.total
                    // }
                    
                    context.commit("set_common", ["dialog_new", false])
                    context.dispatch("packet/search", [], {root:true})
                    // context.commit("set_cities", data.records)
                    // context.commit("update_common", data.total)
                }
            } catch (e) {
                context.commit("set_common", ["search_city_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_unit(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                let resp = await api.search_unit(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total
                    }

                    context.commit("set_units", data.records)
                    context.commit("set_common", ["total_unit", data.total])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_level_price(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                let resp = await api.search_level_price(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total
                    }

                    context.commit("set_levels", data.records)
                    context.commit("set_common", ["total_level", data.total])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        }
    }
}