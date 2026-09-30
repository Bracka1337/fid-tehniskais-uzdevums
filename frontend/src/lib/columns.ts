export type DocumentColumn = {
  key: string
  label: string
  sortable?: boolean
  align?: 'left' | 'center' | 'right'
}

export const documentColumns: DocumentColumn[] = [
  { key: 'title', label: 'Nosaukums', sortable: true },
  { key: 'responsible_unit', label: 'Atbildīgā struktūrvienība' },
  { key: 'created_on', label: 'Izveides datums', sortable: true, align: 'center' },
  { key: 'file_type', label: 'Faila tips', align: 'center' },
  {
    key: 'reading_time_minutes',
    label: 'Lasīšanas laiks (min)',
    sortable: true,
    align: 'center',
  },
  { key: 'importance', label: 'Svarīgums', align: 'center' },
  { key: 'category', label: 'Kategorija' },
  { key: 'is_active', label: 'Aktīvs', align: 'center' },
  { key: 'url', label: 'Saite', align: 'right' },
]

export const sortableColumns = documentColumns.filter((column) => column.sortable)
