import { Link } from 'react-router-dom';
import { Card } from '@/shared/ui/Card';

export function HomePage() {
  return (
    <div className="grid gap-6 md:grid-cols-12">
      <div className="md:col-span-8">
        <Card>
          <div className="rounded-xl bg-gradient-to-r from-brand-900 via-brand-800 to-accent-700 p-6 text-white">
            <p className="text-xs uppercase tracking-[0.2em] text-white/80">Colegio Aprende</p>
            <h1 className="mt-2 text-3xl font-extrabold tracking-tight">Educacion en linea para secundaria y preparatoria</h1>
            <p className="mt-3 text-sm text-white/90">
              Tu avance academico en un solo lugar: registro, pagos, calendario, materias y evaluaciones.
            </p>
            <div className="mt-6 flex flex-wrap gap-3">
              <Link to="/register" className="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-brand-900 hover:bg-slate-100">
                Iniciar registro
              </Link>
              <Link to="/login" className="rounded-lg border border-white/60 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">
                Ya tengo cuenta
              </Link>
            </div>
          </div>
        </Card>
      </div>

      <div className="md:col-span-4">
        <Card>
          <h2 className="text-lg font-bold text-slate-900">Ruta del estudiante</h2>
          <ol className="mt-4 list-decimal space-y-2 pl-5 text-sm text-slate-700">
            <li>Registro como prospecto</li>
            <li>Pago de inscripcion</li>
            <li>Activacion de cuenta estudiante</li>
            <li>Completar perfil y foto</li>
            <li>Seguimiento de calendario y materias</li>
          </ol>
        </Card>
      </div>
    </div>
  );
}
