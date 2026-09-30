import type { ChangeEvent } from 'react'
import { sortableColumns } from '../lib/columns'
import { categoryLabels, importanceLabels } from '../lib/labels'
import type { DocumentFilters } from '../types/document'

type Props = {
  filters: DocumentFilters
  onChange: (next: DocumentFilters) => void
}

export function DocumentFilters({ filters, onChange }: Props) {
  const handleChange =
    <K extends keyof DocumentFilters>(key: K) =>
    (event: ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
      onChange({
        ...filters,
        [key]: event.target.value as DocumentFilters[K],
      })
    }

  const handleSortChange = (event: ChangeEvent<HTMLSelectElement>) => {
    const [sort, direction] = event.target.value.split(':') as [
      string,
      DocumentFilters['direction'],
    ]

    onChange({
      ...filters,
      sort,
      direction,
    })
  }

  return (
    <div className="documents__filters">
      <div className="documents__filters-main">
        <div className="field">
          <label htmlFor="search">Meklēt</label>
          <input
            id="search"
            type="search"
            value={filters.search}
            placeholder="Nosaukums, apraksts, struktūrvienība"
            onChange={handleChange('search')}
          />
        </div>

        <div className="field">
          <label htmlFor="importance">Svarīgums</label>
          <select
            id="importance"
            value={filters.importance}
            onChange={handleChange('importance')}
          >
            <option value="">Visi</option>
            {Object.entries(importanceLabels).map(([value, label]) => (
              <option key={value} value={value}>
                {label}
              </option>
            ))}
          </select>
        </div>

        <div className="field">
          <label htmlFor="category">Kategorija</label>
          <select
            id="category"
            value={filters.category}
            onChange={handleChange('category')}
          >
            <option value="">Visas</option>
            {Object.entries(categoryLabels).map(([value, label]) => (
              <option key={value} value={value}>
                {label}
              </option>
            ))}
          </select>
        </div>

        <div className="field">
          <label htmlFor="is_active">Aktīvs</label>
          <select
            id="is_active"
            value={filters.is_active}
            onChange={handleChange('is_active')}
          >
            <option value="">Visi</option>
            <option value="1">Jā</option>
            <option value="0">Nē</option>
          </select>
        </div>
      </div>

      <div className="field documents__filters-sort">
        <label htmlFor="sort">Kārtot pēc</label>
        <select
          id="sort"
          value={`${filters.sort}:${filters.direction}`}
          onChange={handleSortChange}
        >
          {sortableColumns.flatMap((column) => [
            <option key={`${column.key}:asc`} value={`${column.key}:asc`}>
              {column.label} ↑
            </option>,
            <option key={`${column.key}:desc`} value={`${column.key}:desc`}>
              {column.label} ↓
            </option>,
          ])}
        </select>
      </div>
    </div>
  )
}
