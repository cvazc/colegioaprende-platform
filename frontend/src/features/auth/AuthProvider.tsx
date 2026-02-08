import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import { loginRequest, logoutRequest, meRequest } from '@/shared/api/auth-api';
import { getStoredSession, setStoredSession } from '@/shared/lib/storage';
import type { AuthSession, LoginPayload } from '@/shared/types/auth';

type AuthContextValue = {
  session: AuthSession | null;
  loading: boolean;
  login: (payload: LoginPayload) => Promise<void>;
  logout: () => Promise<void>;
  hasPermission: (ability: string) => boolean;
};

const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [session, setSession] = useState<AuthSession | null>(() => getStoredSession());
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    let mounted = true;

    async function hydrate() {
      const stored = getStoredSession();
      if (!stored?.token) {
        if (mounted) {
          setLoading(false);
        }
        return;
      }

      try {
        const me = await meRequest();
        const next: AuthSession = {
          token: stored.token,
          role: me.role === 'unknown' ? stored.role : me.role,
          user: me.user,
        };

        if (mounted) {
          setSession(next);
          setStoredSession(next);
        }
      } catch {
        if (mounted) {
          setSession(null);
          setStoredSession(null);
        }
      } finally {
        if (mounted) {
          setLoading(false);
        }
      }
    }

    hydrate();

    return () => {
      mounted = false;
    };
  }, []);

  const login = useCallback(async (payload: LoginPayload) => {
    const next = await loginRequest(payload);
    setSession(next);
    setStoredSession(next);
  }, []);

  const logout = useCallback(async () => {
    try {
      await logoutRequest();
    } finally {
      setSession(null);
      setStoredSession(null);
    }
  }, []);

  const hasPermission = useCallback(
    (ability: string) => {
      if (!session || session.role !== 'employee') {
        return false;
      }

      const permissions = (session.user as { permissions?: Record<string, boolean> }).permissions;
      return Boolean(permissions?.[ability]);
    },
    [session]
  );

  const value = useMemo(
    () => ({
      session,
      loading,
      login,
      logout,
      hasPermission,
    }),
    [session, loading, login, logout, hasPermission]
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error('useAuth must be used within AuthProvider');
  }

  return context;
}
