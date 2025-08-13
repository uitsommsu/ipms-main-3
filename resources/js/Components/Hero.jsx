import { motion } from "framer-motion";
import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';

export default function Hero() {
  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.3
      }
    }
  };

  const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: {
      opacity: 1,
      y: 0,
      transition: {
        duration: 0.6,
        ease: "easeOut"
      }
    }
  };

  return (
    <section className="relative bg-gradient-to-br from-green-900 to-green-700 text-white py-32 px-6 overflow-hidden">
      <div className="absolute inset-0 bg-[url('/images/pattern.png')] opacity-10"></div>
      
      <motion.div 
        className="relative max-w-6xl mx-auto text-center"
        variants={containerVariants}
        initial="hidden"
        animate="visible"
      >
        <motion.div variants={itemVariants}>
          <h1 className="text-5xl md:text-6xl font-bold mb-6 leading-tight">
            Streamline Your <span className="text-green-300">IP Management</span>
            <br />Journey with IPMS
          </h1>
        </motion.div>

        <motion.div variants={itemVariants}>
          <p className="text-xl md:text-2xl mb-8 max-w-2xl mx-auto text-gray-200 leading-relaxed">
            Empowering researchers and administrators with a comprehensive platform 
            for intellectual property submission, management, and tracking.
          </p>
        </motion.div>

        <motion.div 
          className="flex flex-col sm:flex-row justify-center gap-4 sm:gap-6"
          variants={itemVariants}
        >
          <a
            href={route('filament.dashboard.auth.login')}
            target="_blank"
            className="inline-flex items-center justify-center px-8 py-4 bg-white text-green-700 font-semibold rounded-lg hover:bg-green-50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
          >
            Submit IP Application
            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
              <path fillRule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clipRule="evenodd" />
            </svg>
          </a>
          
          <a
            href={route('filament.dashboard.auth.register')}
            target="_blank"
            className="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-green-700 transition-all duration-300"
          >
            Create an Account
          </a>
        </motion.div>
      </motion.div>
    </section>
  );
}
