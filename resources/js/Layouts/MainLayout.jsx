import Footer from "../Components/Footer";
import Navbar from "../Components/Navbar";
export default function MainLayout({ children }) {
  return (
    <div className="font-sans min-h-screen flex flex-col">
     <Navbar />
      <main className="flex-grow">{children}</main>
      {/* Optional global footer can go here */}
       <Footer />
    </div>
   
  );
}
