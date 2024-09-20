export interface User {
    id?: bigint
    username?: string
    email?: string
    email_verified_at?: string | null
    created_at?: string
    updated_at?: string
}

enum LeadStatusType {
    NEW = 'NEW',
    PENDING = 'PENDING',
    DONE = 'DONE',
}

export interface Lead {
    id?: bigint
    first_name?: string
    last_name?: string
    phone?: string
    email?: string
    appeal?: string
    status?: LeadStatusType
    created_at?: string
    updated_at?: string
}
