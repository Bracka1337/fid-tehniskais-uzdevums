import { useEffect, useRef, useState } from 'react'
import { documentColumns } from '../lib/columns'
import { categoryLabels, importanceLabels } from '../lib/labels'
import type { Document, DocumentFilters } from '../types/document'

type Props = {
  documents: Document[]
  sort: string
  direction: DocumentFilters['direction']
  onSort: (column: string) => void
  loading: boolean
  emptyMessage?: string
}

export function DocumentTable({
  documents,
  sort,
  direction,
  onSort,
  loading,
  emptyMessage = 'Dokumenti nav atrasti.',
}: Props) {
  const wrapRef = useRef<HTMLDivElement>(null)
  const [canScrollLeft, setCanScrollLeft] = useState(false)
  const [canScrollRight, setCanScrollRight] = useState(false)

  useEffect(() => {
    const element = wrapRef.current
    if (!element) {
      return
    }

    const updateShadows = () => {
      const maxScroll = Math.max(0, element.scrollWidth - element.clientWidth)
      setCanScrollLeft(element.scrollLeft > 1)
      setCanScrollRight(element.scrollLeft < maxScroll - 1)
    }

    updateShadows()
    element.addEventListener('scroll', updateShadows, { passive: true })
    window.addEventListener('resize', updateShadows)

    const observer = new ResizeObserver(updateShadows)
    observer.observe(element)
    if (element.firstElementChild) {
      observer.observe(element.firstElementChild)
    }

    return () => {
      element.removeEventListener('scroll', updateShadows)
      window.removeEventListener('resize', updateShadows)
      observer.disconnect()
    }
  }, [documents])

  const shellClassName = [
    'documents__table-shell',
    canScrollLeft ? 'documents__table-shell--shadow-left' : '',
    canScrollRight ? 'documents__table-shell--shadow-right' : '',
    loading ? 'documents__table-shell--loading' : '',
  ]
    .filter(Boolean)
    .join(' ')

  const cellClassName = (align?: 'left' | 'center' | 'right') =>
    align && align !== 'left' ? `documents__cell--${align}` : undefined

  return (
    <div className={shellClassName}>
      <div ref={wrapRef} className="documents__table-wrap">
        <table>
          <thead>
            <tr>
              {documentColumns.map((column) => (
                <th key={column.key} className={cellClassName(column.align)}>
                  {column.sortable ? (
                    <button
                      type="button"
                      className="sortable"
                      onClick={() => onSort(column.key)}
                    >
                      {column.label}
                      {sort === column.key
                        ? direction === 'asc'
                          ? ' ↑'
                          : ' ↓'
                        : ''}
                    </button>
                  ) : (
                    column.label
                  )}
                </th>
              ))}
            </tr>
          </thead>
          <tbody>
            {documents.length === 0 ? (
              <tr className="documents__empty-row">
                <td colSpan={documentColumns.length}>{emptyMessage}</td>
              </tr>
            ) : (
              documents.map((document) => (
                <tr key={document.id}>
                  <td>
                    <strong>{document.title}</strong>
                    {document.description ? (
                      <>
                        <br />
                        <span>{document.description}</span>
                      </>
                    ) : null}
                  </td>
                  <td>{document.responsible_unit}</td>
                  <td className={cellClassName('center')}>{document.created_on}</td>
                  <td className={cellClassName('center')}>{document.file_type}</td>
                  <td className={cellClassName('center')}>
                    {document.reading_time_minutes}
                  </td>
                  <td className={cellClassName('center')}>
                    {importanceLabels[document.importance]}
                  </td>
                  <td>{categoryLabels[document.category]}</td>
                  <td className={cellClassName('center')}>
                    <span
                      className={`badge ${document.is_active ? 'badge--active' : 'badge--inactive'}`}
                    >
                      {document.is_active ? 'Jā' : 'Nē'}
                    </span>
                  </td>
                  <td className={cellClassName('right')}>
                    <a href={document.url} target="_blank" rel="noreferrer">
                      Atvērt
                    </a>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  )
}
