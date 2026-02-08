import { NavLink, Outlet, useNavigate } from 'react-router-dom';
import { useAuth } from '@/features/auth/AuthProvider';
import { Button } from '@/shared/ui/Button';

export function StudentLayout() {
  const { session, logout } = useAuth();
  const navigate = useNavigate();

  return (
    <div className="min-h-screen bg-slate-100">
      <header className="border-b border-slate-200 bg-white">
        <div className="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-3">
          <div>
            <p className="text-xs uppercase tracking-wide text-slate-500">Panel Estudiante</p>
            <p className="text-sm font-semibold text-slate-800">{session?.user.first_name} {session?.user.surnames}</p>
          </div>
          <div className="flex items-center gap-2 text-sm">
            <NavLink to="/app">Dashboard</NavLink>
            <NavLink to="/app/subjects">Materias</NavLink>
            <NavLink to="/app/payments">Pagos</NavLink>
            <NavLink to="/app/calendar">Calendario</NavLink>
            <NavLink to="/app/cohort">Cohorte</NavLink>
            <NavLink to="/app/onboarding">Perfil</NavLink>
            <Button
              variant="secondary"
              onClick={async () => {
                await logout();
                navigate('/login', { replace: true });
              }}
            >
              Salir
            </Button>
          </div>
        </div>
      </header>
      <main className="mx-auto max-w-7xl px-4 py-6">
        <Outlet />
      </main>
    </div>
  );
}
