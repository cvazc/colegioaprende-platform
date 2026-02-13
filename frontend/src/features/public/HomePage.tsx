import { FormEvent, useMemo, useState } from 'react';
import { Link } from 'react-router-dom';
import { Button } from '@/shared/ui/Button';
import { Card } from '@/shared/ui/Card';
import { Input } from '@/shared/ui/Input';
import { Reveal } from '@/shared/ui/Reveal';

type ProgramItem = {
  name: string;
  subtitle: string;
  description: string;
  meta: string;
};

type PricePlan = {
  title: string;
  total: string;
  items: string[];
};

const programs: ProgramItem[] = [
  {
    name: 'Preparatoria',
    subtitle: 'Certificacion oficial de preparatoria',
    description:
      'Programa de acreditacion y certificacion en 4 meses, con 16 cuestionarios y horario flexible para personas con trabajo.',
    meta: '64 horas - 16 cuestionarios',
  },
  {
    name: 'Secundaria',
    subtitle: 'Certificacion oficial de secundaria',
    description:
      'Plan de estudio a distancia con acompanamiento academico para concluir secundaria con validez oficial.',
    meta: '64 horas - 16 cuestionarios',
  },
  {
    name: 'Licenciaturas',
    subtitle: 'Programas universitarios en linea',
    description:
      'Opciones de continuidad academica para estudiar desde casa con soporte y seguimiento personalizado.',
    meta: 'Modalidad en linea - ritmo flexible',
  },
  {
    name: 'Idiomas y habilidades',
    subtitle: 'Ingles y herramientas digitales',
    description:
      'Cursos practicos para fortalecer perfil profesional y academico, estudiando desde celular o computadora.',
    meta: 'Contenido practico - enfoque aplicado',
  },
];

const benefits = [
  'Estudia desde cualquier lugar',
  'Certificacion oficial',
  'Sin horarios rigidos',
  'Atencion personalizada',
  'Asesorias academicas',
  'Aceptacion para universidad o trabajo',
];

const pricingData: Record<'preparatoria' | 'secundaria', PricePlan[]> = {
  preparatoria: [
    {
      title: 'Estudiante Preferencial',
      total: '$11,570.00 MXN',
      items: [
        'Incluido Inscripcion',
        '$250.00 MXN Cuestionario',
        'Incluido Registro',
        'Incluido Material didactico',
        '$690.00 MXN Garantia',
        '$4,490.00 MXN Cuestionario final',
        '$2,390.00 MXN Certificacion',
      ],
    },
    {
      title: 'Estudiante General',
      total: '$22,879.00 MXN',
      items: [
        '$1,390.00 MXN Inscripcion',
        '$425.00 MXN Cuestionario',
        '$1,630.00 MXN Registro',
        '$3,249.00 MXN Material didactico',
        '$1,390.00 MXN Garantia',
        '$5,230.00 MXN Cuestionario final',
        '$3,190.00 MXN Certificacion',
      ],
    },
    {
      title: 'Estudiante Exclusivo',
      total: '$9,997.00 MXN',
      items: [
        'Incluido Inscripcion',
        'Incluido Cuestionario',
        'Incluido Registro',
        'Incluido Material didactico',
        'Incluido Garantia',
        'Incluido Cuestionario final',
        'Incluido Certificacion',
      ],
    },
  ],
  secundaria: [
    {
      title: 'Estudiante Preferencial',
      total: '$9,370.00 MXN',
      items: [
        'Incluido Inscripcion',
        '$250.00 MXN Cuestionario',
        'Incluido Registro',
        '$0.00 MXN Material didactico',
        '$690.00 MXN Garantia',
        '$2,490.00 MXN Cuestionario final',
        '$2,190.00 MXN Certificacion',
      ],
    },
    {
      title: 'Estudiante General',
      total: '$21,879.00 MXN',
      items: [
        '$1,390.00 MXN Inscripcion',
        '$425.00 MXN Cuestionario',
        '$1,630.00 MXN Registro',
        '$3,249.00 MXN Material didactico',
        '$1,390.00 MXN Garantia',
        '$4,230.00 MXN Cuestionario final',
        '$3,190.00 MXN Certificacion',
      ],
    },
    {
      title: 'Estudiante Exclusivo',
      total: '$8,390.00 MXN',
      items: [
        'Incluido Inscripcion',
        'Incluido Cuestionario',
        'Incluido Registro',
        'Incluido Material didactico',
        'Incluido Garantia',
        'Incluido Cuestionario final',
        'Incluido Certificacion',
      ],
    },
  ],
};

const testimonials = [
  {
    name: 'Jose Perez',
    city: 'Veracruz, Ver.',
    age: '51 anos',
    text: 'Por trabajo no encontraba como terminar mis estudios. Este sistema fue practico y hoy tengo mi certificado.',
  },
  {
    name: 'Lizbeth Sanchez',
    city: 'CDMX',
    age: '30 anos',
    text: 'Me ayudo mucho que fuera en linea porque mis turnos cambiaban. Termine y ya me inscribi a la universidad.',
  },
  {
    name: 'Everardo Villapando',
    city: 'Guadalajara, Jal.',
    age: '45 anos',
    text: 'Fue la mejor decision. Pense que no podria concluir, pero el acompanamiento me ayudo a mantener el ritmo.',
  },
];

const faqs = [
  {
    question: 'Como me inscribo?',
    answer: 'Completa el formulario de aplicacion, recibe seguimiento y finaliza tu proceso de ingreso en linea.',
  },
  {
    question: 'Que requisitos necesito?',
    answer: 'Identificacion oficial, datos personales y la informacion academica solicitada para validar tu proceso.',
  },
  {
    question: 'Los certificados tienen validez oficial?',
    answer: 'Si. Nuestros programas estan orientados a procesos con validez oficial segun normativa aplicable.',
  },
  {
    question: 'Puedo estudiar si trabajo?',
    answer: 'Si. La plataforma esta pensada para adultos con horarios variables y aprendizaje a su propio ritmo.',
  },
];

function FeatureIcon() {
  return (
    <svg viewBox="0 0 24 24" className="h-6 w-6 text-accent-500" fill="none" stroke="currentColor" strokeWidth="2">
      <path d="M5 12l4 4L19 6" />
    </svg>
  );
}

export function HomePage() {
  const [level, setLevel] = useState<'preparatoria' | 'secundaria'>('preparatoria');
  const [formSent, setFormSent] = useState(false);

  const activePlans = useMemo(() => pricingData[level], [level]);

  function onQuickFormSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setFormSent(true);
  }

  return (
    <div className="space-y-12">
      <section id="inicio" className="grid gap-6 md:grid-cols-12">
        <Reveal className="md:col-span-7">
          <div className="rounded-2xl bg-gradient-to-br from-brand-900 via-brand-800 to-accent-700 p-8 text-white shadow-lg">
            <p className="text-xs uppercase tracking-[0.2em] text-white/80">Colegio Aprende</p>
            <h1 className="mt-3 text-3xl font-extrabold leading-tight md:text-4xl">
              Estudia Preparatoria o Licenciatura 100% en linea y avanza a tu ritmo
            </h1>
            <p className="mt-4 text-sm text-white/90 md:text-base">
              Consigue tu certificado oficial con acompanamiento academico, horarios flexibles y una plataforma pensada
              para personas que trabajan.
            </p>
            <div className="mt-6 flex flex-wrap gap-3">
              <Link to="/register" className="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-brand-900 hover:bg-slate-100">
                Inscribete hoy
              </Link>
              <a
                href="https://wa.me/525500000000?text=Hola%20quiero%20asesoria"
                target="_blank"
                rel="noreferrer"
                className="rounded-lg border border-white/60 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10"
              >
                Contacta un asesor por WhatsApp
              </a>
            </div>
          </div>
        </Reveal>

        <Reveal className="md:col-span-5" delayMs={80}>
          <Card>
            <h2 className="text-lg font-bold text-slate-900 dark:text-slate-100">Agenda tu asesoria gratuita</h2>
            <p className="mt-1 text-sm text-slate-600 dark:text-slate-300">Dejanos tus datos y te orientamos en el programa ideal.</p>

            <form className="mt-4 grid gap-3" onSubmit={onQuickFormSubmit}>
              <Input placeholder="Nombre" required />
              <Input placeholder="Correo" type="email" required />
              <Input placeholder="WhatsApp" required />
              <Button type="submit">Solicitar informacion</Button>
              {formSent && <p className="text-sm text-emerald-700">Gracias. Te redirigimos al registro completo.</p>}
            </form>

            <div className="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
              Video introductorio (proximamente): veras como funciona el aula virtual en menos de 60 segundos.
            </div>
          </Card>
        </Reveal>
      </section>

      <section id="por-que" className="space-y-5">
        <Reveal>
          <h2 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Por que estudiar con nosotros</h2>
        </Reveal>
        <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
          {benefits.map((item, index) => (
            <Reveal key={item} delayMs={index * 60}>
              <Card>
                <div className="flex items-start gap-3">
                  <FeatureIcon />
                  <p className="text-sm font-semibold text-slate-800 dark:text-slate-100">{item}</p>
                </div>
              </Card>
            </Reveal>
          ))}
        </div>
        <Reveal>
          <div className="flex flex-wrap gap-3">
            <Link to="/register" className="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
              Solicita mas informacion
            </Link>
            <a
              href="https://wa.me/525500000000?text=Hola%20quiero%20inscribirme"
              target="_blank"
              rel="noreferrer"
              className="rounded-lg border border-brand-200 px-4 py-2 text-sm font-semibold text-brand-800 hover:bg-brand-50 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800"
            >
              Hablar con asesor
            </a>
          </div>
        </Reveal>
      </section>

      <section id="programas" className="space-y-5">
        <Reveal>
          <h2 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Programas de certificacion en linea</h2>
        </Reveal>
        <div className="grid gap-4 md:grid-cols-2">
          {programs.map((program, index) => (
            <Reveal key={program.name} delayMs={index * 80}>
              <Card>
                <p className="text-xs uppercase tracking-wide text-brand-700 dark:text-accent-300">{program.name}</p>
                <h3 className="mt-1 text-lg font-bold text-slate-900 dark:text-slate-100">{program.subtitle}</h3>
                <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">{program.description}</p>
                <p className="mt-3 text-xs font-semibold text-slate-500 dark:text-slate-400">{program.meta}</p>
                <div className="mt-4 flex gap-3">
                  <Link to="/register" className="rounded-lg bg-brand-700 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                    Inscribete
                  </Link>
                  <a href="/#contacto" className="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800">
                    Ver mas
                  </a>
                </div>
              </Card>
            </Reveal>
          ))}
        </div>
      </section>

      <section id="como-funciona" className="space-y-5">
        <Reveal>
          <h2 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Como funciona</h2>
        </Reveal>
        <div className="grid gap-4 md:grid-cols-4">
          {['Registro', 'Pago', 'Onboarding', 'Avance y certificacion'].map((step, index) => (
            <Reveal key={step} delayMs={index * 70}>
              <Card>
                <p className="text-xs text-slate-500 dark:text-slate-400">Paso {index + 1}</p>
                <p className="mt-1 text-sm font-bold text-slate-800 dark:text-slate-100">{step}</p>
              </Card>
            </Reveal>
          ))}
        </div>
      </section>

      <section id="precios" className="space-y-5">
        <Reveal>
          <h2 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Conoce nuestros costos</h2>
        </Reveal>
        <Reveal>
          <div className="flex flex-wrap gap-2">
            <button
              type="button"
              onClick={() => setLevel('preparatoria')}
              className={`rounded-lg px-4 py-2 text-sm font-semibold ${
                level === 'preparatoria'
                  ? 'bg-brand-700 text-white'
                  : 'border border-slate-300 text-slate-700 dark:border-slate-600 dark:text-slate-100'
              }`}
            >
              Preparatoria
            </button>
            <button
              type="button"
              onClick={() => setLevel('secundaria')}
              className={`rounded-lg px-4 py-2 text-sm font-semibold ${
                level === 'secundaria'
                  ? 'bg-brand-700 text-white'
                  : 'border border-slate-300 text-slate-700 dark:border-slate-600 dark:text-slate-100'
              }`}
            >
              Secundaria
            </button>
          </div>
        </Reveal>
        <div className="grid gap-4 lg:grid-cols-3">
          {activePlans.map((plan, index) => (
            <Reveal key={plan.title} delayMs={index * 80}>
              <Card>
                <h3 className="text-lg font-bold text-slate-900 dark:text-slate-100">{plan.title}</h3>
                <p className="mt-1 text-sm font-semibold text-brand-700 dark:text-accent-300">Total: {plan.total}</p>
                <ul className="mt-3 list-disc space-y-1 pl-5 text-sm text-slate-600 dark:text-slate-300">
                  {plan.items.map((item) => (
                    <li key={item}>{item}</li>
                  ))}
                </ul>
                <Link to="/register" className="mt-4 inline-block rounded-lg bg-accent-500 px-3 py-2 text-sm font-semibold text-white hover:bg-accent-600">
                  Quiero inscribirme
                </Link>
              </Card>
            </Reveal>
          ))}
        </div>
      </section>

      <section id="testimonios" className="space-y-5">
        <Reveal>
          <h2 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Testimonios de alumnos</h2>
        </Reveal>
        <div className="grid gap-4 md:grid-cols-3">
          {testimonials.map((item, index) => (
            <Reveal key={item.name} delayMs={index * 80}>
              <Card>
                <p className="text-sm text-slate-700 dark:text-slate-200">"{item.text}"</p>
                <p className="mt-4 text-sm font-bold text-slate-900 dark:text-slate-100">{item.name}</p>
                <p className="text-xs text-slate-500 dark:text-slate-400">{item.age} - {item.city}</p>
              </Card>
            </Reveal>
          ))}
        </div>
      </section>

      <section id="faqs" className="space-y-5">
        <Reveal>
          <h2 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Preguntas frecuentes</h2>
        </Reveal>
        <div className="grid gap-3">
          {faqs.map((item, index) => (
            <Reveal key={item.question} delayMs={index * 60}>
              <details className="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                <summary className="cursor-pointer text-sm font-semibold text-slate-900 dark:text-slate-100">{item.question}</summary>
                <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">{item.answer}</p>
              </details>
            </Reveal>
          ))}
        </div>
      </section>

      <section id="contacto" className="pb-8">
        <Reveal>
          <Card>
            <div className="grid gap-4 md:grid-cols-2 md:items-center">
              <div>
                <h2 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Listo para comenzar?</h2>
                <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">
                  Inicia tu proceso hoy mismo o habla con un asesor para resolver tus dudas.
                </p>
              </div>
              <div className="flex flex-wrap gap-3 md:justify-end">
                <Link to="/register" className="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                  Aplicar ahora
                </Link>
                <a
                  href="https://wa.me/525500000000?text=Hola%20quiero%20asesoria"
                  target="_blank"
                  rel="noreferrer"
                  className="rounded-lg border border-brand-300 px-4 py-2 text-sm font-semibold text-brand-800 hover:bg-brand-50 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800"
                >
                  WhatsApp asesor
                </a>
              </div>
            </div>
          </Card>
        </Reveal>
      </section>
    </div>
  );
}
