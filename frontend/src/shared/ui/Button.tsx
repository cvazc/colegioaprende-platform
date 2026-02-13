import clsx from 'clsx';
import type { ButtonHTMLAttributes } from 'react';

type Props = ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: 'primary' | 'secondary' | 'danger';
};

export function Button({ className, variant = 'primary', ...props }: Props) {
  return (
    <button
      className={clsx(
        'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-50',
        {
          'bg-brand-600 text-white hover:bg-brand-700': variant === 'primary',
          'bg-slate-200 text-slate-900 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600': variant === 'secondary',
          'bg-rose-600 text-white hover:bg-rose-700': variant === 'danger',
        },
        className
      )}
      {...props}
    />
  );
}
