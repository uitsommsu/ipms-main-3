import { motion, AnimatePresence } from 'framer-motion';

export default function LatestInventions({patents}) {
  
  // Animation variants
  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.2,
      },
    },
  };

  const cardVariants = {
    hidden: { opacity: 0, y: 50 },
    visible: {
      opacity: 1,
      y: 0,
      transition: {
        duration: 0.5,
        ease: "easeOut",
      },
    },
  };

  return (
    <section id="latest-patents" className="py-20 bg-gradient-to-b from-gray-50 to-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <motion.div
          className="text-center mb-16"
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
        >
          <h2 className="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight">
            Latest Inventions
          </h2>
          <p className="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
            Discover groundbreaking patents recently filed and approved by our innovative researchers.
          </p>
        </motion.div>

        {/* Cards Grid */}
        <motion.div
          className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3"
          variants={containerVariants}
          initial="hidden"
          animate="visible"
        >
          {patents.map((patent) => (
            <motion.div
              key={patent.id}
              variants={cardVariants}
              whileHover={{
                y: -10,
                boxShadow: "0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)",
                transition: { duration: 0.3 },
              }}
              className="relative bg-white rounded-2xl shadow-lg overflow-hidden group"
            >
              {/* Image */}
              <div className="relative h-56 overflow-hidden">
                <img
                  src={patent.images[0]}
                  alt={patent.invention}
                  className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
              </div>

              {/* Content */}
              <div className="p-6">
                <h3 className="text-xl font-semibold text-gray-900 mb-3 line-clamp-2">
                  {patent.invention}
                </h3>
                <div className="text-gray-600 text-base leading-relaxed line-clamp-3"
                   dangerouslySetInnerHTML={{ __html: patent.description }}
                />
                <motion.a
                  href="#"
                  className="mt-4 inline-block text-green-600 font-medium text-sm uppercase tracking-wide opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                  whileHover={{ x: 5 }}
                >
                  Learn More →
                </motion.a>
              </div>
            </motion.div>
          ))}
        </motion.div>
      </div>
    </section>
  );
}