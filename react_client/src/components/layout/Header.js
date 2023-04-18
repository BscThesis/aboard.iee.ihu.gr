import { FontAwesomeIcon } from "@fortawesome/react-fontawesome"
import { faLightbulb, faMoon, faSun, faArrowRightFromBracket, faUser } from "@fortawesome/free-solid-svg-icons"
import React, { useEffect, useState } from "react"
import { Link } from "react-router-dom"
import config from "../../config"
import i18n from "../../i18n"
import user from "../../helpers/user"
import Container from 'react-bootstrap/Container'
import Nav from 'react-bootstrap/Nav'
import Navbar from 'react-bootstrap/Navbar'
import NavDropdown from 'react-bootstrap/NavDropdown'
import elFlag from '../../assets/images/flags/gr.png'
import enFlag from '../../assets/images/flags/en.png'
import "bootstrap/js/src/collapse.js"

const Header = (props) => {

    const [darkTheme, setDarkTheme] = useState(
        props.darkThemeEnabled
    )

    useEffect(() => {
        props.setThemeColors(darkTheme)
        localStorage.setItem('dark-theme', darkTheme)
    }, [darkTheme])
    // return (
    //     <>
    //       <Navbar bg="dark" variant="dark">
    //         <Container>
    //           <Navbar.Brand href="#home">Navbar</Navbar.Brand>
    //           <Nav className="me-auto">
    //             <Nav.Link href="#home">Home</Nav.Link>
    //             <Nav.Link href="#features">Features</Nav.Link>
    //             <Nav.Link href="#pricing">Pricing</Nav.Link>
    //           </Nav>
    //         </Container>
    //       </Navbar>
    //     </> <a onClick={() => setDarkTheme(!darkTheme)}><FontAwesomeIcon icon={darkTheme ? faSun : faMoon}/></a>
    //   )
    return (
            <Navbar collapseOnSelect expand="md" bg="dark" variant="dark">
                <Container>
                    <Navbar.Brand to="/">Aboard</Navbar.Brand>
                    <Navbar.Toggle aria-controls="responsive-navbar-nav" />
                    <Navbar.Collapse id="responsive-navbar-nav">
                        <Nav className="me-auto">
                            <Nav.Link as={Link} to="/announcements">{i18n.t('Ανακοινώσεις')}</Nav.Link>
                            {
                                (user.user.is_admin === 1 || user.user.is_author === 1) &&
                                <Nav.Link as={Link} to="/my_announcements">{i18n.t('Διαχείριση ανακοινώσεων')}</Nav.Link>
                            }
                            {
                                (user.user.is_admin === 1) &&
                                <>
                                    {/* <Nav.Link as={Link} to="/tags">{i18n.t('Ετικέτες')}</Nav.Link>
                                    <Nav.Link as={Link} to="/issues">{i18n.t('Issues')}</Nav.Link> */}
                                </>
                            }
                            <Nav.Link as={Link} to="/about">{i18n.t('About')}</Nav.Link>
                            <NavDropdown title={<img src={i18n.language === 'el' ? elFlag : enFlag} />} id="collasible-nav-dropdown-locale">
                                    <Nav.Link onClick={() => i18n.setLanguage('el')}><img src={elFlag} /> Ελληνικά</Nav.Link>
                                    <Nav.Link onClick={() => i18n.setLanguage('en')}><img src={enFlag} /> English</Nav.Link>
                            </NavDropdown>
                        </Nav>
                        <Nav>
                            {
                                !props.user ?
                                <Nav.Link onClick={props.loginProp}>{i18n.t('Σύνδεση')}</Nav.Link> :
                                <NavDropdown title={props.user.name} id="collasible-nav-dropdown-account">
                                    <Nav.Link as={Link} to="/account"> <FontAwesomeIcon icon={faUser} /> {i18n.t('Λογαριασμός')}</Nav.Link>
                                    <Nav.Link onClick={props.logoutProp}><FontAwesomeIcon icon={faArrowRightFromBracket} /> {i18n.t('Αποσύνδεση')}</Nav.Link>
                                </NavDropdown>
                                
                            }
                        </Nav>
                    </Navbar.Collapse>
                </Container>
            </Navbar>
            
    )
}

export default Header