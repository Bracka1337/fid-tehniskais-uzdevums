import { useState } from 'react'
import { login } from '../api/auth'
import type { User } from '../types/document'

type Props = {
  onSuccess: (user: User) => void
}

export function LoginPage({ onSuccess }: Props) {
  const [email, setEmail] = useState('reviewer@example.com')
  const [password, setPassword] = useState('password')
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (formData: FormData) => {
    setLoading(true)
    setError(null)

    const nextEmail = String(formData.get('email') ?? email)
    const nextPassword = String(formData.get('password') ?? password)

    try {
      const user = await login(nextEmail, nextPassword)
      onSuccess(user)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Pieteikšanās neizdevās')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="login">
      <div className="login__panel">
        <p className="login__brand">Dokumentu katalogs</p>
        <h1 className="login__title">Pieteikšanās</h1>
        <p className="login__subtitle">
          Autorizējieties, lai skatītu dokumentu metadatus.
        </p>

        <form className="login__form" action={handleSubmit}>
          <div className="field">
            <label htmlFor="email">E-pasts</label>
            <input
              id="email"
              name="email"
              type="email"
              autoComplete="username"
              value={email}
              onChange={(event) => setEmail(event.target.value)}
              required
            />
          </div>

          <div className="field">
            <label htmlFor="password">Parole</label>
            <input
              id="password"
              name="password"
              type="password"
              autoComplete="current-password"
              value={password}
              onChange={(event) => setPassword(event.target.value)}
              required
            />
          </div>

          {error ? <p className="status status--error">{error}</p> : null}

          <div className="login__actions">
            <button className="btn" type="submit" disabled={loading}>
              {loading ? 'Piesakās…' : 'Pieteikties'}
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}
