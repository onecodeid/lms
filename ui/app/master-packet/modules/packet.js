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

        packets: [],
        total_packet: 0,
        total_packet_page: 0,
        selected_packet: null,

        current_page: 1
    },
    mutations: {
        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search(state, search) {
            state.search = search
        },

        update_packets(state, data) {
            state.packets = data
        },

        set_selected_packet(state, val) {
            state.selected_packet = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_packet(state, val) {
            state.total_packet = val
        },

        update_total_packet_page(state, val) {
            state.total_packet_page = val
        },

        update_current_page(state, val) {
            state.current_page = val
        }
    },
    actions: {
        async search(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.page = context.state.current_page
                let resp = await api.search(prm)
                console.log(resp)
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

                    context.commit("update_packets", data.records)
                    context.commit("update_total_packet", data.total)
                    context.commit("update_total_packet_page", data.total_page)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async del(context) {
            context.commit("update_search_status", 1)
            console.log("Starting to delete...")
            try {
                let prm = {
                    token: one_token(),
                    id: context.state.selected_packet.M_PacketID
                }
                console.log(prm)
                // prm.token = 
                // prm.id = 

                let resp = await api.del(prm)
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
        }
    }
}