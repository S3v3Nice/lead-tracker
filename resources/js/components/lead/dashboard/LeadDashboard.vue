<script setup lang='ts'>
import {useToast} from 'primevue/usetoast'
import DataTable, {type DataTablePageEvent, type DataTableSortEvent} from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Dropdown, {type DropdownPassThroughOptions} from 'primevue/dropdown'
import {computed, ref} from 'vue'
import axios, {type AxiosError} from 'axios'
import {getErrorMessageByCode, getFullDate, getRelativeDate, ToastHelper} from '@/helpers'
import Dialog from 'primevue/dialog'
import EditLeadForm from '@/components/lead/dashboard/EditLeadForm.vue'
import Skeleton from 'primevue/skeleton'
import type {Lead} from '@/types'
import {LeadStatusType} from '@/types'

interface LoadResponseData {
    success: boolean
    message?: string
    errors?: string[][]
    records?: Lead[]
    status_counts: {
        NEW: number
        PENDING: number
        DONE: number
    }
    pagination?: {
        current_page: number
        total_pages: number
    }
}

const toastHelper = new ToastHelper(useToast())
const isLoading = ref(false)
const records = ref<Lead[]>([])
const statusCounts = ref<Object>()
const totalCount = computed(() => {
    return statusCounts.value
        ? Object.values(statusCounts.value).reduce((sum, current) => sum + current, 0)
        : 0
})
const loadRecordsData = ref({
    page: 1,
    per_page: 10,
    sort_field: 'created_at',
    sort_order: -1
})
const currentRecord = ref<Lead | null>(null)
const isEditRecordModal = ref(false)
const isDeleteRecordModal = ref(false)
const statuses = ref([
    {label: 'Новый', value: LeadStatusType.NEW, severity: 'info'},
    {label: 'В работе', value: LeadStatusType.PENDING, severity: 'warning'},
    {label: 'Завершён', value: LeadStatusType.DONE, severity: 'success'},
])

const statusDropdownPassThroughOptions: DropdownPassThroughOptions = {
    root: {
        style: 'background: none; border: none; box-shadow: none; outline: none;'
    },
    input: {
        style: 'padding: 0; font-size: 0.875rem;',
    },
    trigger: {
        style: 'width: 2rem;'
    },
}

loadRecords()

function loadRecords() {
    isLoading.value = true
    records.value = []

    axios.get('/api/leads', {params: loadRecordsData.value}).then((response) => {
        const data: LoadResponseData = response.data
        if (data.success) {
            records.value = data.records!
            statusCounts.value = data.status_counts
        } else {
            toastHelper.error(data.message || '')
        }
    }).catch((error: AxiosError) => {
        toastHelper.error(getErrorMessageByCode(error.response!.status))
    }).finally(() => {
        isLoading.value = false
    })
}

function getStatusLabel(statusType: LeadStatusType) {
    return statuses.value.find((statusInfo) => statusInfo.value === statusType)?.label
}

function getStatusSeverity(statusType: LeadStatusType) {
    return statuses.value.find((statusInfo) => statusInfo.value === statusType)?.severity
}

function onChangePage(event: DataTablePageEvent) {
    loadRecordsData.value.page = event.page + 1
    loadRecords()
}

function onSort(event: DataTableSortEvent) {
    loadRecordsData.value.sort_field = <string>event.sortField!
    loadRecordsData.value.sort_order = event.sortOrder!
    loadRecordsData.value.page = 1
    loadRecords()
}

function deleteRecord(record: Lead) {
    axios.delete(`/api/leads/${record.id}/`).then((response) => {
        const data: LoadResponseData = response.data
        if (data.success) {
            loadRecords()
            toastHelper.success(`Пользователь ${record.username} удалён.`)
        } else {
            toastHelper.error(data.message || '')
        }
    }).catch((error: AxiosError) => {
        toastHelper.error(getErrorMessageByCode(error.response!.status))
    })
}

function onEditClick(record: Lead) {
    currentRecord.value = {...record}
    isEditRecordModal.value = true
}

function onDeleteClick(record: Lead) {
    currentRecord.value = record
    isDeleteRecordModal.value = true
}

function onConfirmDeleteClick() {
    deleteRecord(currentRecord.value!)
    isDeleteRecordModal.value = false
}

function onRecordSave() {
    loadRecords()
    isEditRecordModal.value = false
}

function updateStatus(record: Lead) {
    axios.put(`/api/leads/${record.id}/status`, {status: record.status!.type}).then((response) => {
        const data = response.data
        if (data.success) {
            toastHelper.success('Статус успешно изменён!')
            loadRecords()
        } else {
            toastHelper.error(data.message || '')
        }
    }).catch((error: AxiosError) => {
        toastHelper.error(getErrorMessageByCode(error.response!.status))
    })
}
</script>

<template>
    <div class="surface-overlay p-4 rounded-xl border">
        <p class="text-3xl font-semibold mb-2">Лиды</p>

        <span class="flex gap-1">
            <span class="text-muted">Всего:</span>
            <Skeleton v-if="isLoading" width="2rem"/>
            <span v-else>{{totalCount}}</span>
        </span>
        <span class="flex gap-6 mt-2">
            <span v-for="status of statuses" class="flex gap-1 justify-center">
                <Tag :value="status.label" :severity="status.severity"/>
                <Skeleton v-if="isLoading" width="2rem"/>
                <span v-else>{{statusCounts[status.value]}}</span>
            </span>
        </span>
    </div>

    <div class="p-4 surface-overlay rounded-xl border mt-3">
        <DataTable
            :value="isLoading ? new Array(3) : records"
            lazy
            paginator
            removable-sort
            :rows="loadRecordsData.per_page"
            :total-records="totalCount"
            @page="onChangePage"
            @sort="onSort"
        >
            <Column :sortable="true" field="id" header="ID">
                <template #body="{ data, field }: {data: Lead, field: string}">
                    <Skeleton v-if="isLoading" width="2rem"/>
                    <template v-else>{{ data[field] }}</template>
                </template>
            </Column>
            <Column :sortable="true" field="first_name" header="Имя">
                <template #body="{ data, field }: {data: Lead, field: string}">
                    <Skeleton v-if="isLoading" width="5rem"/>
                    <template v-else>{{ data[field] }}</template>
                </template>
            </Column>
            <Column :sortable="true" field="last_name" header="Фамилия">
                <template #body="{ data, field }: {data: Lead, field: string}">
                    <Skeleton v-if="isLoading" width="5rem"/>
                    <template v-else>{{ data[field] }}</template>
                </template>
            </Column>
            <Column :sortable="true" field="phone" header="Телефон">
                <template #body="{ data, field }: {data: Lead, field: string}">
                    <Skeleton v-if="isLoading" width="7rem"/>
                    <template v-else>{{ data[field] }}</template>
                </template>
            </Column>
            <Column :sortable="true" field="email" header="E-mail">
                <template #body="{ data, field }: {data: Lead, field: string}">
                    <Skeleton v-if="isLoading" width="10rem"/>
                    <template v-else>{{ data[field] }}</template>
                </template>
            </Column>
            <Column :sortable="true" field="created_at" header="Дата">
                <template #body="{ data, field }: {data: Lead, field: string}">
                    <Skeleton v-if="isLoading" width="6.5rem"/>
                    <template v-else>
                        <p v-tooltip.top="getFullDate(data[field])">{{ getRelativeDate(data[field]) }}</p>
                    </template>
                </template>
            </Column>
            <Column field="status" header="Статус">
                <template #body="{ data, field }: {data: Lead, field: string}">
                    <Skeleton v-if="isLoading" width="6.5rem"/>
                    <Dropdown
                        v-else
                        v-model="data.status!.type"
                        :options="statuses"
                        option-label="label"
                        option-value="value"
                        :pt="statusDropdownPassThroughOptions"
                        class="w-[7rem]"
                        @change="updateStatus(data)"
                    >
                        <template #value="{value, placeholder}: {value: LeadStatusType}">
                            <Tag :value="getStatusLabel(value)" :severity="getStatusSeverity(value)"/>
                        </template>
                        <template #option="{option, index}">
                            <Tag :value="option.label" :severity="option.severity"/>
                        </template>
                    </Dropdown>
                </template>
            </Column>
            <Column>
                <template #body="{ data }: {data: Lead}">
                    <div class="flex gap-2">
                        <template v-if="isLoading">
                            <Skeleton shape="circle" size="2.5rem"/>
                            <Skeleton shape="circle" size="2.5rem"/>
                        </template>
                        <template v-else>
                            <Button
                                icon="fa-solid fa-pen"
                                outlined
                                rounded
                                title="Редактировать"
                                @click="onEditClick(data)"
                            />
                            <Button
                                icon="fa-solid fa-trash"
                                outlined
                                rounded
                                severity="danger"
                                title="Удалить"
                                @click="onDeleteClick(data)"
                            />
                        </template>
                    </div>
                </template>
            </Column>
        </DataTable>

        <Dialog
            header="Редактирование лида"
            position="top"
            :visible="isEditRecordModal"
            :modal="true"
            :draggable="false"
            :dismissable-mask="true"
            @update:visible="() => isEditRecordModal = !isEditRecordModal"
            class="w-[35rem]"
        >
            <EditLeadForm :lead="currentRecord" @cancel="() => isEditRecordModal = false" @processed="onRecordSave"/>
        </Dialog>

        <Dialog
            header="Подтверждение удаления"
            position="top"
            :visible="isDeleteRecordModal"
            :modal="true"
            :draggable="false"
            :dismissable-mask="true"
            @update:visible="() => isDeleteRecordModal = !isDeleteRecordModal"
            class="w-[30rem]"
        >
            <div class="flex">
                <span class="fa-solid fa-triangle-exclamation mr-3" style="font-size: 2rem"/>
                Вы действительно хотите удалить данного лида?
            </div>

            <div class="flex justify-end gap-2 mt-2">
                <Button label="Отмена" severity="secondary" @click="() => { isDeleteRecordModal = false }"></Button>
                <Button label="Удалить" @click="onConfirmDeleteClick" severity="danger"></Button>
            </div>
        </Dialog>
    </div>
</template>

<style scoped>

</style>
