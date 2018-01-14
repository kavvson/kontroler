<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Przychody
 *
 * @author Kavvson
 */
class PDF_Generator extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        // if (!$this->ion_auth->in_group(array(1,2)))
        //{
        //    die("Brak uprawnień");
        // }
    }

    public function Place($rodzaj,$month = null, $rok = null){
        $this->load->model("Generatorpdf_model", "p");
        $this->load->helpers("Helpers");

        if($month === null || $rok === null){
            $month = date("m");
            $rok = date("Y");
        }
        $data = array(
            'title' => "Dokument - " . $rodzaj,
        );
        switch ($rodzaj) {
            case "WyplatyPracownikow" :
                $this->load->model("Statistic_model");
                $data['z'] = $this->p->pobierz_wyplaty_pracownikow($month, $rok);

                $data['miesiac'] = Statistic_model::mnum_mname($month,'unquote');
                $data['rok'] = $rok;

                $html = $this->load->view('pdfy/wyplaty', $data, true);
                $pdfFilePath = "Wynagrodzenie-".$data['miesiac'] ."/".$rok.".pdf";
                break;
            default :
                show_error("Błąd", 404, "Brak dokumentu");
                break;
        }
        $this->load->library('M_pdf');
        $this->m_pdf->pdf->SetDisplayMode('fullpage');
        $this->m_pdf->pdf->list_indent_first_level = 0; // 1 or 0 - whether to indent the first level of a list
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "I");
    }

    public function Dokument($rodzaj, $numer)
    {
        $this->load->model("Generatorpdf_model", "p");
        $this->load->helpers("Helpers");



        $data = array(
            'title' => "Dokument - " . $rodzaj,
        );
        switch ($rodzaj) {
            case "Delegacje" :
                $data['dele'] = $this->p->pobierz_delegacje($numer);
                $html = $this->load->view('pdfy/delegacja', $data, true);
                $pdfFilePath = "Delegacja-" . $data['dele']["kupujacy"] . "-" . $data['dele']["dstart"] . ".pdf";
                break;
            case "Wyplaty" :
                $data['wypl'] = $this->p->pobierz_wyplaty($numer);
                $html = $this->load->view('pdfy/wyplaty_doreki', $data, true);
                $pdfFilePath = "Wyplaty-" . $data['wypl']["kupujacy"] . "-" . $data['wypl']["zarejestrowano"] . ".pdf";
                break;
            case "Zaliczka" :
                $data['z'] = $this->p->pobierz_zaliczke($numer);
                $html = $this->load->view('pdfy/zaliczka', $data, true);
                $pdfFilePath = "Zaliczka-" . $data['z']["kupujacy"] . "-" . $data['z']["data_operacji"] . ".pdf";
                break;
            default :
                show_error("Błąd", 404, "Brak dokumentu");
                break;
        }


        //load mPDF library
        $this->load->library('M_pdf');


        $this->m_pdf->pdf->SetDisplayMode('fullpage');

        $this->m_pdf->pdf->list_indent_first_level = 0; // 1 or 0 - whether to indent the first level of a list
        //generate the PDF from the given html
        $this->m_pdf->pdf->WriteHTML($html);

        //download it.
        $this->m_pdf->pdf->Output($pdfFilePath, "I");
        // $this->load->view('przychody/wzor_faktury', $data);
    }


}
