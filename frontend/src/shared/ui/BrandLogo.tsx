import clsx from 'clsx';
import logoFull from '@/shared/assets/branding/colegio-aprende-logo.svg';

type BrandLogoProps = {
  className?: string;
  alt?: string;
};

export function BrandLogo({ className, alt = 'Colegio Aprende - Educacion en Linea' }: BrandLogoProps) {
  return <img src={logoFull} alt={alt} className={clsx('h-10 w-auto', className)} />;
}
