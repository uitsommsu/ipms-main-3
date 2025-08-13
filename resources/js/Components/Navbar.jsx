import { useState } from "react";
import { Bars3Icon, XMarkIcon } from "@heroicons/react/24/outline";
import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { motion, AnimatePresence } from 'framer-motion';

export default function Navbar() {
  const [menuOpen, setMenuOpen] = useState(false);
  const toggleMenu = () => setMenuOpen(!menuOpen);

  const menuItems = [
    { name: "Home", href: route('home') },
    { name: "Inventions", href: "#features" },
    { name: "Utility Models", href: "#how-it-works" },
    { name: "About IPMS", href: "#preview" },
  ];

  // Animation variants
  const menuVariants = {
    hidden: { opacity: 0, y: -20 },
    visible: { 
      opacity: 1, 
      y: 0,
      transition: { 
        duration: 0.3,
        staggerChildren: 0.1 
      }
    },
    exit: { 
      opacity: 0, 
      y: -20,
      transition: { duration: 0.2 }
    }
  };

  const menuItemVariants = {
    hidden: { opacity: 0, x: -20 },
    visible: { opacity: 1, x: 0 },
  };

  return (
    <nav className="bg-white shadow-lg fixed w-full z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-20 items-center">
          {/* Logo */}
          <motion.div 
            className="flex items-center"
            initial={{ opacity: 0, x: -20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ duration: 0.5 }}
          >
          
            <Link href={route('home')} className="flex items-center">
              <img 
              src="/images/ipms.png" 
              alt="IPMS Logo" 
              className="h-20 w-auto transition-transform duration-300 hover:scale-105" 
            />
              <span className="hidden xl:block  text-xl font-bold text-green-800">Intellectual Property Management System</span>
          </Link>
            
          </motion.div>

          {/* Desktop Menu */}
          <div className="hidden md:flex items-center space-x-10">
            {menuItems.map(({ name, href }) => (
              <motion.div
                key={name}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5 }}
              >
                <Link
                  href={href}
                  className="relative text-gray-800 font-semibold text-sm uppercase tracking-wide hover:text-green-600 transition-colors duration-300 group"
                >
                  <span>{name}</span>
                  <span className="absolute left-0 -bottom-1 w-0 h-0.5 bg-green-600 transition-all duration-300 group-hover:w-full"></span>
                </Link>
              </motion.div>
            ))}
            <motion.a
              href={route('filament.dashboard.auth.login')}
              target="_blank"
              ininitial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5 }}
              className="ml-6 px-6 py-2 rounded-full bg-green-600 text-white font-semibold text-sm uppercase tracking-wide hover:bg-green-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
              
            >
              Login
            </motion.a>
          </div>

          {/* Mobile Menu Toggle */}
          <motion.div 
            className="md:hidden"
            whileTap={{ scale: 0.9 }}
          >
            <button 
              onClick={toggleMenu} 
              className="focus:outline-none p-2 rounded-md hover:bg-gray-100 transition-colors duration-200"
              aria-label="Toggle menu"
            >
              {menuOpen ? (
                <XMarkIcon className="h-6 w-6 text-gray-800" />
              ) : (
                <Bars3Icon className="h-6 w-6 text-gray-800" />
              )}
            </button>
          </motion.div>
        </div>
      </div>

      {/* Mobile Menu */}
      <AnimatePresence>
        {menuOpen && (
          <motion.div 
            className="md:hidden bg-white shadow-xl px-6 py-4"
            variants={menuVariants}
            initial="hidden"
            animate="visible"
            exit="exit"
          >
            {menuItems.map(({ name, href }) => (
              <motion.a
                key={name}
                href={href}
                onClick={toggleMenu}
                className="block py-3 text-gray-800 font-semibold text-base uppercase tracking-wide hover:text-green-600 transition-colors duration-200 border-b border-gray-100 last:border-b-0"
                variants={menuItemVariants}
              >
                {name}
              </motion.a>
            ))}
            <motion.a
              href={route('filament.dashboard.auth.login')}
              target="_blank"
              onClick={toggleMenu}
              className="block mt-4 bg-green-600 text-white px-6 py-3 rounded-full text-center font-semibold text-sm uppercase tracking-wide hover:bg-green-700 transition-all duration-300 shadow-md hover:shadow-lg"
              variants={menuItemVariants}
            >
              Login
            </motion.a>
          </motion.div>
        )}
      </AnimatePresence>
    </nav>
  );
}