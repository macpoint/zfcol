<?php

/**
 * PDF generator controller
 * 
 * @package zfcol
 * @category application/controllers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class PdfController extends Zend_Controller_Action {

    public function init() {}

    /**
     * Display controller view only
     * 
     */
    public function indexAction() {}

    /**
     * Generate the PDF file
     * 
     * @uses TCPDF - PHP class to generate PDF files from HTML (http://www.tcpdf.org)
     * @param string $what all / favorite
     * @param string $type I = view, D = download
     */
    private function _createPdft($what = 'all', $type = 'I') {
        
        // reset default layout
        Zend_Layout::resetMvcInstance();
        
        // load MPDF library
        Zend_loader::loadFile('tcpdf.php', APPLICATION_PATH . "/../data/tcpdf");

        // get movies
        $movies = new Application_Model_Movies();
        $list = $what == 'all' ? $movies->fetchAll() : $movies->getFavoriteMovies();
        $title = $what == 'all' ? $this->view->translate('All movies') : $this->view->translate('Favorite movies');

        // load pdf model to get header & footer
        $pdfmodel = new Application_Model_Pdf();
        $table = '';
        // generate table cells for all movies
        foreach ($list as $movie) {
            $table .= '<tr nobr="true">
                        <td width="7%">' . $movie->id . '</td>
                        <td width="10%">' . $movie->ownid . '</td>
                        <td width="25%">' . $movie->name . '</td>
                        <td width="38%">' . $movie->genre . '</td>
                        <td width="11%">' . $movie->rating . '%</td>
                        <td width="9%">' . $this->view->mediatype($movie->media) . '</td>
                      </tr>';
        }

        $html = $pdfmodel->getTableHeader() . $table . $pdfmodel->getTableFooter();

        // generate pdf
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(Zend_Registry::get('AppInfo')->getTag());
        $pdf->SetAuthor(Zend_Registry::get('AppInfo')->getAuthor());
        $pdf->SetTitle($title);
        $pdf->SetSubject($title);

        // set default header data
        $pdf->SetHeaderData('', '', $title . ' (' . count($list) . ')', Zend_Registry::get('AppInfo')->getName());

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 7));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin('12');
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // add a page
        $pdf->AddPage();

        $pdf->SetFont('freesans', '', 7);
        $pdf->writeHTML($html, true, false, false, false, 'C');
        $pdf->Output(Zend_Registry::get('AppInfo')->getTag() . '-' . $what . '.pdf', $type);
    }

    /**
     * Generate PDF action
     * 
     * @throws Zend_Exception
     */
    public function generateAction() {
        
        // reset default layout 
        Zend_Layout::resetMvcInstance();
        
        $what = $this->getRequest()->getParam('what');
        if (empty($what))
            throw new Zend_Exception('Invalid parameter "what"');

        $type = $this->getRequest()->getParam('type');
        if (empty($what))
            throw new Zend_Exception('Invalid parameter "type"');

        $this->_createPdft($what, $type);
        $this->_forward('index');
    }

}