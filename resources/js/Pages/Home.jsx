import { motion, AnimatePresence } from "framer-motion";
import Footer from "../Components/Footer";
import Hero from "../Components/Hero";
import LatestInventions from "../Components/LatestInventions";
import LatestUtilityModels from "../Components/LatestUtilityModels";
import MainLayout from "../Layouts/MainLayout";

export default function Home({patents, utilityModels}) {
  const pageVariants = {
    initial: {
      opacity: 0,
      y: 20
    },
    animate: {
      opacity: 1,
      y: 0,
      transition: {
        duration: 0.5,
        staggerChildren: 0.2
      }
    }
  };

  const sectionVariants = {
    initial: {
      opacity: 0,
      y: 20
    },
    animate: {
      opacity: 1,
      y: 0
    },
    exit: {
      opacity: 0,
      y: -20,
      transition: {
        duration: 0.3
      }
    }
  };
  return(
    <MainLayout>
      <AnimatePresence mode="wait">
      <motion.div
        key="home-content"
          initial={false}  // Prevent initial animation flash
          animate="animate"
          exit="exit"
        variants={pageVariants}
      >
        <motion.div variants={sectionVariants}>
          <Hero />
        </motion.div>
        
        <motion.div variants={sectionVariants}>
          <LatestInventions patents={patents} />
        </motion.div>

        <motion.div variants={sectionVariants}>
          <LatestUtilityModels utilityModels={utilityModels} />
        </motion.div>

        
      </motion.div>
      </AnimatePresence>
    </MainLayout>
     
    
  );
}