// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_expense.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        
        expenses: [],
        total_expense: 0,
        // total_expense_page: 0,
        selected_expense: {},

        expense_date: new Date().toISOString().substr(0, 10),
        expense_amount: 0,
        expense_note: '',
        expense_name: '',
        expense_code: '',

        // current_page: 1
        dialog_new: false
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

        set_expenses(state, data) {
            state.expenses = data
        },

        set_selected_expense(state, val) {
            state.selected_expense = val
        },

        update_search_status(state, val) {
            state.search_status = val
        }
    },
    actions: {
        async search_expense(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.search = context.state.search
                let resp = await api.search_expense(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_expenses", resp.data.records)
                    context.commit("set_common", ['total_expense', resp.data.total])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async save(context, x) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.expense_date = context.state.expense_date
                prm.expense_amount = context.state.expense_amount
                prm.expense_note = context.state.expense_note
                

                if (context.state.edit)
                    prm.expense_id = context.rootState.expense.selected_expense.expense_id

                let resp_m = null
                if (x) {
                    if (x.add_mode) {
                        resp_m = await api.save_m_expense({
                            token: one_token(),
                            expense_name: context.state.expense_name,
                            expense_code: context.state.expense_code
                        })        
                    }
                }
                
                if (resp_m)
                    prm.expense_m_id = resp_m.data
                else
                    prm.expense_m_id = context.state.selected_expense.M_ExpenseID
                let resp = await api.save(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.dispatch('expense/search', {}, {root:true})
                    context.commit('set_common', ['dialog_new', false])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async save_m_expense(context) {
            var c = context
            context.dispatch('m_expense_new/save', null, {root:true})
            // await context.dispatch('search_expense')
        }
    }
}