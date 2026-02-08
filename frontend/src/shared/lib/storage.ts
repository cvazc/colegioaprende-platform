import type { AuthSession } from '@/shared/types/auth';

const KEY = 'colegioaprende.session';

export function getStoredSession(): AuthSession | null {
  try {
    const raw = localStorage.getItem(KEY);
    if (!raw) {
      return null;
    }
    return JSON.parse(raw) as AuthSession;
  } catch {
    return null;
  }
}

export function setStoredSession(session: AuthSession | null): void {
  if (!session) {
    localStorage.removeItem(KEY);
    return;
  }

  localStorage.setItem(KEY, JSON.stringify(session));
}
