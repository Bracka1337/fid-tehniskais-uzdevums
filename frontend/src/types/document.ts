export type Importance = 'low' | 'medium' | 'high' | 'critical'
export type Category = 'public' | 'internal' | 'restricted' | 'confidential'

export type Document = {
  id: number
  external_id: string
  title: string
  description: string | null
  responsible_unit: string
  created_on: string
  url: string
  file_type: string
  reading_time_minutes: number
  importance: Importance
  category: Category
  is_active: boolean
}

export type DocumentFilters = {
  search: string
  importance: '' | Importance
  category: '' | Category
  is_active: '' | '1' | '0'
  sort: string
  direction: 'asc' | 'desc'
}

export type DocumentsResponse = {
  data: Document[]
  meta: {
    total: number
  }
}

export type User = {
  id: number
  name: string
  email: string
}
