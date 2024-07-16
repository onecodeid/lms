// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_level.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',

        levels: [],
        total_level: 0,
        total_level_page: 0,
        selected_level: {},

        current_page: 1
    },
    mutations: {
        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search(state, search) {
            state.search = search
        },

        update_levels(state, data) {
            state.levels = data
        },

        set_selected_level(state, val) {
            state.selected_level = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_level(state, val) {
            state.total_level = val
        },

        update_total_level_page(state, val) {
            state.total_level_page = val
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
                prm.search = context.state.search
                prm.page = context.state.current_page
                prm.include_inactive = true
                let resp = await api.search(prm)

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

                    context.commit("update_levels", data.records)
                    context.commit("update_total_level", data.total)
                    context.commit("update_total_level_page", data.total_page)
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
                prm.id = context.state.selected_level.M_CustomerLevelID

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
        },

        async restore(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.id = context.state.selected_level.M_CustomerLevelID

                let resp = await api.restore(prm)
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