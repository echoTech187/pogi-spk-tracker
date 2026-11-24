/**
 * POGI SKP TRACKER - OFFICIAL TEMPLATE ENGINE FOR ONE POGI MONTHLY REPORT
 * Menghasilkan PDF Laporan Bulanan ONE POGI 100% Persis, Rapi, & Indah sesuai Monthly_Report_ONE_POGI_Revisi_01.pdf
 * Termasuk progress bar gradien, tipografi resmi ReportLab, kop surat, tabel rapi tanpa text clipping.
 */

(function(global) {
  'use strict';

  // Objek 1 sampai 24 (Definisi font Helvetica, ZapfDingbats, Shading gradien Capaian, MediaBox, Metadata)
  const OBJS_1_TO_24 = {"1": "\n/F1 2 0 R /F2 3 0 R /F3 13 0 R\n", "2": "\n/BaseFont /Helvetica /Encoding /WinAnsiEncoding /Name /F1 /Subtype /Type1 /Type /Font\n", "3": "\n/BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding /Name /F2 /Subtype /Type1 /Type /Font\n", "4": "\n/ColorSpace /DeviceRGB /Coords [ 0 0 37.39906 0 ] /Extend [true true] /Function 17 0 R /ShadingType 2\n", "5": "\n/ColorSpace /DeviceRGB /Coords [ 0 0 20.99596 0 ] /Extend [true true] /Function 18 0 R /ShadingType 2\n", "6": "\n/ColorSpace /DeviceRGB /Coords [ 0 0 9.841857 0 ] /Extend [true true] /Function 19 0 R /ShadingType 2\n", "7": "\n/ColorSpace /DeviceRGB /Coords [ 0 0 6.561238 0 ] /Extend [true true] /Function 20 0 R /ShadingType 2\n", "8": "\n/ColorSpace /DeviceRGB /Coords [ 0 0 16.4031 0 ] /Extend [true true] /Function 21 0 R /ShadingType 2\n", "9": "\n/ColorSpace /DeviceRGB /Coords [ 0 0 13.12248 0 ] /Extend [true true] /Function 22 0 R /ShadingType 2\n", "10": "\n/ColorSpace /DeviceRGB /Coords [ 0 0 7.873486 0 ] /Extend [true true] /Function 23 0 R /ShadingType 2\n", "11": "\n/Contents 25 0 R /MediaBox [ 0 0 595.2756 841.8898 ] /Parent 24 0 R /Resources <<\n/Font 1 0 R /ProcSet [ /PDF /Text /ImageB /ImageC /ImageI ] /Shading <<\n/Sh0 4 0 R /Sh1 5 0 R /Sh2 6 0 R /Sh3 7 0 R /Sh4 8 0 R /Sh5 9 0 R \n  /Sh6 10 0 R\n>>\n>> /Rotate 0 /Trans <<\n\n>> \n  /Type /Page\n", "12": "\n/Contents 26 0 R /MediaBox [ 0 0 595.2756 841.8898 ] /Parent 24 0 R /Resources <<\n/Font 1 0 R /ProcSet [ /PDF /Text /ImageB /ImageC /ImageI ]\n>> /Rotate 0 /Trans <<\n\n>> \n  /Type /Page\n", "13": "\n/BaseFont /ZapfDingbats /Name /F3 /Subtype /Type1 /Type /Font\n", "14": "\n/Contents 27 0 R /MediaBox [ 0 0 595.2756 841.8898 ] /Parent 24 0 R /Resources <<\n/Font 1 0 R /ProcSet [ /PDF /Text /ImageB /ImageC /ImageI ]\n>> /Rotate 0 /Trans <<\n\n>> \n  /Type /Page\n", "15": "\n/PageMode /UseNone /Pages 24 0 R /Type /Catalog\n", "16": "\n/Author (Impruve.Id) /CreationDate (D:20261117173211+07'00') /Creator (\\(unspecified\\)) /Keywords () /ModDate (D:20261117173211+07'00') /Producer (ReportLab PDF Library - \\(opensource\\)) \n  /Subject (\\(unspecified\\)) /Title (Laporan Bulanan ONE POGI) /Trapped /False\n", "17": "\n/C0 [ .8 .88 .96 ] /C1 [ .16 .43 .72 ] /Domain [ 0 1 ] /FunctionType 2 /N 1\n", "18": "\n/C0 [ .8 .88 .96 ] /C1 [ .16 .43 .72 ] /Domain [ 0 1 ] /FunctionType 2 /N 1\n", "19": "\n/C0 [ .8 .88 .96 ] /C1 [ .16 .43 .72 ] /Domain [ 0 1 ] /FunctionType 2 /N 1\n", "20": "\n/C0 [ .8 .88 .96 ] /C1 [ .16 .43 .72 ] /Domain [ 0 1 ] /FunctionType 2 /N 1\n", "21": "\n/C0 [ .8 .88 .96 ] /C1 [ .16 .43 .72 ] /Domain [ 0 1 ] /FunctionType 2 /N 1\n", "22": "\n/C0 [ .8 .88 .96 ] /C1 [ .16 .43 .72 ] /Domain [ 0 1 ] /FunctionType 2 /N 1\n", "23": "\n/C0 [ .8 .88 .96 ] /C1 [ .16 .43 .72 ] /Domain [ 0 1 ] /FunctionType 2 /N 1\n", "24": "\n/Count 3 /Kids [ 11 0 R 12 0 R 14 0 R ] /Type /Pages\n"};

  // Template Content Stream Halaman 1, 2, 3 (Decompressed PDF Streams)
  const STREAM_P1_TEMPLATE = "1 0 0 1 0 0 cm  BT /F1 12 Tf 14.4 TL ET\nq\n.086275 .227451 .372549 rg\nn 56.69291 762.6898 481.8898 25 re f*\n1 1 1 rg\nBT /F2 7.3 Tf 8.76 TL ET\nBT 1 0 0 1 65.7 770.8898 Tm (IMPRUVE.ID) Tj T* ET\nBT 1 0 0 1 184.7 770.8898 Tm (MONTHLY REPORT | ONE POGI) Tj T* ET\nQ\nq\n.835294 .870588 .909804 RG\n.5 w\nn 56.69291 39.68976 m 538.5827 39.68976 l S\nBT /F1 7 Tf 8.4 TL ET\n.356863 .396078 .45098 rg\nBT 1 0 0 1 56.69291 26.88976 Tm (IMPRUVE.ID | MONTHLY REPORT ONE POGI) Tj T* ET\nBT 1 0 0 1 504.7377 26.88976 Tm (Halaman 1) Tj T* ET\nQ\nq\n1 0 0 1 56.69291 723.8898 cm\nq\n.086275 .227451 .372549 rg\nBT 1 0 0 1 0 4 Tm /F2 22 Tf 26 TL (LAPORAN BULANAN) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 718.8898 cm\nQ\nq\n1 0 0 1 56.69291 705.8898 cm\nq\n.356863 .396078 .45098 rg\nBT 1 0 0 1 0 3 Tm /F1 10 Tf 13 TL (Pengelolaan dan Penginputan Data Satuan Kredit Profesi \\(SKP\\) Dokter) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 692.8898 cm\nQ\nq\n1 0 0 1 56.69291 623.8898 cm\nq\n.917647 .94902 .972549 rg\nn 0 69 82.2 -69 re f*\n.917647 .94902 .972549 rg\nn 240.9449 69 82.2 -69 re f*\n0 0 0 rg\nBT /F1 10 Tf 12 TL ET\nq\n1 0 0 1 6 52.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Periode laporan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 88.2 52.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Bulan Tahun]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 246.9449 52.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Tanggal laporan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 329.1449 52.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Tanggal]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 6 29.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Nama program) Tj T* ET\nQ\nQ\nq\n1 0 0 1 88.2 29.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (ONE POGI) Tj T* ET\nQ\nQ\nq\n1 0 0 1 246.9449 29.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (PIC pelaksana) Tj T* ET\nQ\nQ\nq\n1 0 0 1 329.1449 29.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama PIC]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 6 6.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Pelaksana) Tj T* ET\nQ\nQ\nq\n1 0 0 1 88.2 6.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Impruve.Id) Tj T* ET\nQ\nQ\nq\n1 0 0 1 246.9449 6.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Unit/area) Tj T* ET\nQ\nQ\nq\n1 0 0 1 329.1449 6.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Jika relevan]) Tj T* ET\nQ\nQ\nq\n1 J\n1 j\n.835294 .870588 .909804 RG\n.45 w\nn 0 46 m 481.8898 46 l S\nn 0 23 m 481.8898 23 l S\nn 0 0 m 481.8898 0 l S\nn 0 69 m 481.8898 69 l S\nn 0 0 m 0 69 l S\nn 481.8898 0 m 481.8898 69 l S\nQ\nQ\nQ\nq\n1 0 0 1 56.69291 609.8898 cm\nQ\nq\n1 0 0 1 56.69291 594.8898 cm\nq\n.086275 .227451 .372549 rg\nBT 1 0 0 1 0 3 Tm /F2 12 Tf 15 TL (1. RINGKASAN EKSEKUTIF) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 588.8898 cm\nQ\nq\n1 0 0 1 56.69291 564.0898 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 16.1 Tm /F1 8.7 Tf 12.4 TL (Tuliskan gambaran singkat pelaksanaan pekerjaan selama periode laporan: cakupan kegiatan, progres utama, pencapaian,) Tj T* (serta hal penting yang memerlukan perhatian atau koordinasi.) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 556.0898 cm\nQ\nq\n1 0 0 1 56.69291 434.6898 cm\nq\n.086275 .227451 .372549 rg\nn 0 121.4 481.8898 -19.4 re f*\n.956863 .968627 .980392 rg\nn 0 81.6 481.8898 -20.4 re f*\n.956863 .968627 .980392 rg\nn 0 40.8 481.8898 -20.4 re f*\n0 0 0 rg\nBT /F1 10 Tf 12 TL ET\nq\n1 0 0 1 5 107 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Indikator) Tj T* ET\nQ\nQ\nq\n1 0 0 1 224.6693 107 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Jumlah) Tj T* ET\nQ\nQ\nq\n1 0 0 1 292.1339 107 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Keterangan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 87.1 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Total dokter unik yang diproses) Tj T* ET\nQ\nQ\nq\n1 0 0 1 224.6693 87.1 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 292.1339 87.1 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Hitung dokter berbeda, bukan jumlah record kegiatan.) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 66.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Total record keikutsertaan/kegiatan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 224.6693 66.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 292.1339 66.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Satu dokter dapat memiliki lebih dari satu record.) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Data SKP berhasil diinput) Tj T* ET\nQ\nQ\nq\n1 0 0 1 224.6693 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 292.1339 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Input selesai dan tercatat pada sistem.) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Data dalam proses / menunggu validasi) Tj T* ET\nQ\nQ\nq\n1 0 0 1 224.6693 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 292.1339 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Belum dapat dinyatakan selesai.) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Data terkendala / perlu perbaikan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 224.6693 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 292.1339 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Cantumkan alasan pada bagian kendala.) Tj T* ET\nQ\nQ\nq\n1 J\n1 j\n.835294 .870588 .909804 RG\n.45 w\nn 0 81.6 m 481.8898 81.6 l S\nn 0 61.2 m 481.8898 61.2 l S\nn 0 40.8 m 481.8898 40.8 l S\nn 0 20.4 m 481.8898 20.4 l S\nn 0 0 m 481.8898 0 l S\nn 0 0 m 0 121.4 l S\nn 481.8898 0 m 481.8898 121.4 l S\nQ\nQ\nQ\nq\n1 0 0 1 56.69291 420.6898 cm\nQ\nq\n1 0 0 1 56.69291 405.6898 cm\nq\n.086275 .227451 .372549 rg\nBT 1 0 0 1 0 3 Tm /F2 12 Tf 15 TL (2. RINGKASAN PROGRES PEKERJAAN) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 399.6898 cm\nQ\nq\n1 0 0 1 56.69291 219.2898 cm\nq\n.086275 .227451 .372549 rg\nn 0 180.4 481.8898 -19.4 re f*\n.917647 .94902 .972549 rg\nn 130.1102 161 53.00788 -161 re f*\n0 0 0 rg\nBT /F1 10 Tf 12 TL ET\nq\n1 0 0 1 5 166 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (KPI) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.1102 166 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL 11.35508 0 Td (Realisasi) Tj T* -11.35508 0 Td ET\nQ\nQ\nq\n1 0 0 1 188.1181 166 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL 4.012176 0 Td (Target/Basis) Tj T* -4.012176 0 Td ET\nQ\nQ\nq\n1 0 0 1 245.9449 166 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Capaian) Tj T* ET\nQ\nQ\nq\n1 0 0 1 327.8662 166 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Status) Tj T* ET\nQ\nQ\nq\n1 0 0 1 400.1496 166 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Catatan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 144.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Total database anggota) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.1102 144.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL 26.77268 0 Td (5700) Tj T* -26.77268 0 Td ET\nQ\nQ\nq\n1 0 0 1 188.1181 144.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL 31.59158 0 Td (5700) Tj T* -31.59158 0 Td ET\nQ\nQ\nq\n1 0 0 1 245.9449 144 cm\nq\nn 0 0 37.39906 11 re W* n\n/Sh0 sh\nQ\nq\n.14902 .203922 .270588 rg\nBT /F1 7.3 Tf 8.76 TL ET\nBT 1 0 0 1 47.16697 2.4 Tm (100.0%) Tj T* ET\nQ\nQ\nq\n1 0 0 1 327.8662 144.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Baseline) Tj T* ET\nQ\nQ\nq\n1 0 0 1 400.1496 144.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Basis ilustrasi) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 121.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Database terverifikasi) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.1102 121.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL 26.77268 0 Td (3200) Tj T* -26.77268 0 Td ET\nQ\nQ\nq\n1 0 0 1 188.1181 121.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL 31.59158 0 Td (5700) Tj T* -31.59158 0 Td ET\nQ\nQ\nq\n1 0 0 1 245.9449 121 cm\nq\nn 0 0 20.99596 11 re W* n\n/Sh1 sh\nQ\nq\n.14902 .203922 .270588 rg\nBT /F1 7.3 Tf 8.76 TL ET\nBT 1 0 0 1 51.22577 2.4 Tm (56.1%) Tj T* ET\nQ\nQ\nq\n1 0 0 1 327.8662 121.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Perlu percepatan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 400.1496 121.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (56,1% dari total) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 98.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Data belum lengkap) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.1102 98.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL 26.77268 0 Td (1500) Tj T* -26.77268 0 Td ET\nQ\nQ\nq\n1 0 0 1 188.1181 98.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL 31.59158 0 Td (5700) Tj T* -31.59158 0 Td ET\nQ\nQ\nq\n1 0 0 1 245.9449 98 cm\nq\nn 0 0 9.841857 11 re W* n\n/Sh2 sh\nQ\nq\n.14902 .203922 .270588 rg\nBT /F1 7.3 Tf 8.76 TL ET\nBT 1 0 0 1 51.22577 2.4 Tm (26.3%) Tj T* ET\nQ\nQ\nq\n1 0 0 1 327.8662 98.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Tindak lanjut) Tj T* ET\nQ\nQ\nq\n1 0 0 1 400.1496 98.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Lengkapi data wajib) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 75.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Data belum diproses) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.1102 75.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL 26.77268 0 Td (1000) Tj T* -26.77268 0 Td ET\nQ\nQ\nq\n1 0 0 1 188.1181 75.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL 31.59158 0 Td (5700) Tj T* -31.59158 0 Td ET\nQ\nQ\nq\n1 0 0 1 245.9449 75 cm\nq\nn 0 0 6.561238 11 re W* n\n/Sh3 sh\nQ\nq\n.14902 .203922 .270588 rg\nBT /F1 7.3 Tf 8.76 TL ET\nBT 1 0 0 1 51.22577 2.4 Tm (17.5%) Tj T* ET\nQ\nQ\nq\n1 0 0 1 327.8662 75.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Prioritas) Tj T* ET\nQ\nQ\nq\n1 0 0 1 400.1496 75.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Perlu alokasi verifikator) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 52.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Anggota memenuhi SKP) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.1102 52.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL 26.77268 0 Td (2500) Tj T* -26.77268 0 Td ET\nQ\nQ\nq\n1 0 0 1 188.1181 52.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL 31.59158 0 Td (5700) Tj T* -31.59158 0 Td ET\nQ\nQ\nq\n1 0 0 1 245.9449 52 cm\nq\nn 0 0 16.4031 11 re W* n\n/Sh4 sh\nQ\nq\n.14902 .203922 .270588 rg\nBT /F1 7.3 Tf 8.76 TL ET\nBT 1 0 0 1 51.22577 2.4 Tm (43.9%) Tj T* ET\nQ\nQ\nq\n1 0 0 1 327.8662 52.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Monitoring) Tj T* ET\nQ\nQ\nq\n1 0 0 1 400.1496 52.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Perlu validasi bukti) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 29.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Anggota belum memenuhi SKP) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.1102 29.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL 26.77268 0 Td (2000) Tj T* -26.77268 0 Td ET\nQ\nQ\nq\n1 0 0 1 188.1181 29.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL 31.59158 0 Td (5700) Tj T* -31.59158 0 Td ET\nQ\nQ\nq\n1 0 0 1 245.9449 29 cm\nq\nn 0 0 13.12248 11 re W* n\n/Sh5 sh\nQ\nq\n.14902 .203922 .270588 rg\nBT /F1 7.3 Tf 8.76 TL ET\nBT 1 0 0 1 51.22577 2.4 Tm (35.1%) Tj T* ET\nQ\nQ\nq\n1 0 0 1 327.8662 29.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Tindak lanjut) Tj T* ET\nQ\nQ\nq\n1 0 0 1 400.1496 29.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Segmentasi anggota) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 6.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Anggota dalam proses SKP) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.1102 6.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL 26.77268 0 Td (1200) Tj T* -26.77268 0 Td ET\nQ\nQ\nq\n1 0 0 1 188.1181 6.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL 31.59158 0 Td (5700) Tj T* -31.59158 0 Td ET\nQ\nQ\nq\n1 0 0 1 245.9449 6 cm\nq\nn 0 0 7.873486 11 re W* n\n/Sh6 sh\nQ\nq\n.14902 .203922 .270588 rg\nBT /F1 7.3 Tf 8.76 TL ET\nBT 1 0 0 1 51.22577 2.4 Tm (21.1%) Tj T* ET\nQ\nQ\nq\n1 0 0 1 327.8662 6.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Monitoring) Tj T* ET\nQ\nQ\nq\n1 0 0 1 400.1496 6.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Pantau penyelesaian) Tj T* ET\nQ\nQ\nq\n1 J\n1 j\n.835294 .870588 .909804 RG\n.45 w\nn 0 138 m 481.8898 138 l S\nn 0 115 m 481.8898 115 l S\nn 0 92 m 481.8898 92 l S\nn 0 69 m 481.8898 69 l S\nn 0 46 m 481.8898 46 l S\nn 0 23 m 481.8898 23 l S\nn 0 0 m 481.8898 0 l S\nn 0 0 m 0 180.4 l S\nn 481.8898 0 m 481.8898 180.4 l S\nQ\nQ\nQ\nq\n1 0 0 1 56.69291 205.2898 cm\nQ\nq\n1 0 0 1 56.69291 190.2898 cm\nq\n.086275 .227451 .372549 rg\nBT 1 0 0 1 0 3 Tm /F2 12 Tf 15 TL (3. DETAIL PENGINPUTAN DATA DOKTER) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 184.2898 cm\nQ\nq\n1 0 0 1 56.69291 159.4898 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 16.1 Tm /F1 8.7 Tf 12.4 TL (Gunakan tabel ini sebagai ringkasan atau lampiran. Untuk menjaga kerahasiaan, tampilkan nomor identitas/anggota hanya) Tj T* (jika diperlukan, diizinkan, dan sesuai akses yang diberikan POGI.) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 151.4898 cm\nQ\nq\n1 0 0 1 56.69291 70.48976 cm\nq\n.086275 .227451 .372549 rg\nn 0 81 481.8898 -19.4 re f*\n.956863 .968627 .980392 rg\nn 0 30.8 481.8898 -30.8 re f*\n0 0 0 rg\nBT /F1 10 Tf 12 TL ET\nq\n1 0 0 1 5 66.6 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (No.) Tj T* ET\nQ\nQ\nq\n1 0 0 1 29 66.6 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Nama dokter / ID internal) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.0158 66.6 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Wilayah/cabang) Tj T* ET\nQ\nQ\nq\n1 0 0 1 207.2992 66.6 cm\nq\nBT 1 0 0 1 0 2.1 Tm 9.4 TL /F2 7.3 Tf 1 1 1 rg (Kegiatan & tanggal) Tj T* ET\nQ\nQ\nq\n1 0 0 1 298.8583 66.6 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (SKP) Tj T* ET\nQ\nQ\nq\n1 0 0 1 332.5906 66.6 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Tgl. input) Tj T* ET\nQ\nQ\nq\n1 0 0 1 380.7796 66.6 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Status / catatan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 41.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (1) Tj T* ET\nQ\nQ\nq\n1 0 0 1 29 41.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama / ID]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.0158 41.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Wilayah]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 207.2992 36.8 cm\nq\nBT 1 0 0 1 0 11.5 Tm 9.4 TL /F1 7.3 Tf .14902 .203922 .270588 rg ([Nama kegiatan]) Tj  T* ([Tanggal]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 298.8583 41.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 332.5906 41.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 380.7796 41.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Status / catatan]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 10.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (2) Tj T* ET\nQ\nQ\nq\n1 0 0 1 29 10.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama / ID]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.0158 10.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Wilayah]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 207.2992 6 cm\nq\nBT 1 0 0 1 0 11.5 Tm 9.4 TL /F1 7.3 Tf .14902 .203922 .270588 rg ([Nama kegiatan]) Tj  T* ([Tanggal]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 298.8583 10.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 332.5906 10.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 380.7796 10.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Status / catatan]) Tj T* ET\nQ\nQ\nq\n1 J\n1 j\n.835294 .870588 .909804 RG\n.45 w\nn 0 30.8 m 481.8898 30.8 l S\nn 0 0 m 481.8898 0 l S\nn 0 0 m 0 81 l S\nn 481.8898 0 m 481.8898 81 l S\nQ\nQ\nQ\n \n";
  const STREAM_P2_TEMPLATE = "1 0 0 1 0 0 cm  BT /F1 12 Tf 14.4 TL ET\nq\n.835294 .870588 .909804 RG\n.5 w\nn 56.69291 39.68976 m 538.5827 39.68976 l S\nBT /F1 7 Tf 8.4 TL ET\n.356863 .396078 .45098 rg\nBT 1 0 0 1 56.69291 26.88976 Tm (IMPRUVE.ID | MONTHLY REPORT ONE POGI) Tj T* ET\nBT 1 0 0 1 504.7377 26.88976 Tm (Halaman 2) Tj T* ET\nQ\nq\n1 0 0 1 56.69291 618.4898 cm\nq\n.086275 .227451 .372549 rg\nn 0 173.4 481.8898 -19.4 re f*\n.956863 .968627 .980392 rg\nn 0 123.2 481.8898 -30.8 re f*\n.956863 .968627 .980392 rg\nn 0 61.6 481.8898 -30.8 re f*\n0 0 0 rg\nBT /F1 10 Tf 12 TL ET\nq\n1 0 0 1 5 159 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (No.) Tj T* ET\nQ\nQ\nq\n1 0 0 1 29 159 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Nama dokter / ID internal) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.0158 159 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Wilayah/cabang) Tj T* ET\nQ\nQ\nq\n1 0 0 1 207.2992 159 cm\nq\nBT 1 0 0 1 0 2.1 Tm 9.4 TL /F2 7.3 Tf 1 1 1 rg (Kegiatan & tanggal) Tj T* ET\nQ\nQ\nq\n1 0 0 1 298.8583 159 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (SKP) Tj T* ET\nQ\nQ\nq\n1 0 0 1 332.5906 159 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Tgl. input) Tj T* ET\nQ\nQ\nq\n1 0 0 1 380.7796 159 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Status / catatan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 133.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (3) Tj T* ET\nQ\nQ\nq\n1 0 0 1 29 133.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama / ID]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.0158 133.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Wilayah]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 207.2992 129.2 cm\nq\nBT 1 0 0 1 0 11.5 Tm 9.4 TL /F1 7.3 Tf .14902 .203922 .270588 rg ([Nama kegiatan]) Tj  T* ([Tanggal]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 298.8583 133.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 332.5906 133.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 380.7796 133.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Status / catatan]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 103.1 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (4) Tj T* ET\nQ\nQ\nq\n1 0 0 1 29 103.1 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama / ID]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.0158 103.1 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Wilayah]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 207.2992 98.4 cm\nq\nBT 1 0 0 1 0 11.5 Tm 9.4 TL /F1 7.3 Tf .14902 .203922 .270588 rg ([Nama kegiatan]) Tj  T* ([Tanggal]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 298.8583 103.1 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 332.5906 103.1 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 380.7796 103.1 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Status / catatan]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 72.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (5) Tj T* ET\nQ\nQ\nq\n1 0 0 1 29 72.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama / ID]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.0158 72.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Wilayah]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 207.2992 67.6 cm\nq\nBT 1 0 0 1 0 11.5 Tm 9.4 TL /F1 7.3 Tf .14902 .203922 .270588 rg ([Nama kegiatan]) Tj  T* ([Tanggal]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 298.8583 72.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 332.5906 72.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 380.7796 72.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Status / catatan]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 41.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (6) Tj T* ET\nQ\nQ\nq\n1 0 0 1 29 41.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama / ID]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.0158 41.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Wilayah]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 207.2992 36.8 cm\nq\nBT 1 0 0 1 0 11.5 Tm 9.4 TL /F1 7.3 Tf .14902 .203922 .270588 rg ([Nama kegiatan]) Tj  T* ([Tanggal]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 298.8583 41.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 332.5906 41.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 380.7796 41.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Status / catatan]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 10.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (7) Tj T* ET\nQ\nQ\nq\n1 0 0 1 29 10.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama / ID]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 135.0158 10.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Wilayah]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 207.2992 6 cm\nq\nBT 1 0 0 1 0 11.5 Tm 9.4 TL /F1 7.3 Tf .14902 .203922 .270588 rg ([Nama kegiatan]) Tj  T* ([Tanggal]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 298.8583 10.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 332.5906 10.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 380.7796 10.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Status / catatan]) Tj T* ET\nQ\nQ\nq\n1 J\n1 j\n.835294 .870588 .909804 RG\n.45 w\nn 0 154 m 481.8898 154 l S\nn 0 123.2 m 481.8898 123.2 l S\nn 0 92.4 m 481.8898 92.4 l S\nn 0 61.6 m 481.8898 61.6 l S\nn 0 30.8 m 481.8898 30.8 l S\nn 0 0 m 481.8898 0 l S\nn 0 154 m 0 173.4 l S\nn 0 0 m 0 154 l S\nn 481.8898 154 m 481.8898 173.4 l S\nn 481.8898 0 m 481.8898 154 l S\nQ\nQ\nQ\nq\n1 0 0 1 56.69291 604.4898 cm\nQ\nq\n1 0 0 1 56.69291 589.4898 cm\nq\n.086275 .227451 .372549 rg\nBT 1 0 0 1 0 3 Tm /F2 12 Tf 15 TL (4. REKAPITULASI BERDASARKAN KEGIATAN) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 583.4898 cm\nQ\nq\n1 0 0 1 56.69291 473.0898 cm\nq\n.086275 .227451 .372549 rg\nn 0 110.4 481.8898 -28.8 re f*\n.956863 .968627 .980392 rg\nn 0 61.2 481.8898 -20.4 re f*\n.956863 .968627 .980392 rg\nn 0 20.4 481.8898 -20.4 re f*\n0 0 0 rg\nBT /F1 10 Tf 12 TL ET\nq\n1 0 0 1 5 91.3 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (No.) Tj T* ET\nQ\nQ\nq\n1 0 0 1 35 91.3 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Nama kegiatan/webinar) Tj T* ET\nQ\nQ\nq\n1 0 0 1 179.5669 91.3 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Tanggal kegiatan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 261.4882 86.6 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 11.5 Tm /F2 7.3 Tf 9.4 TL (Jumlah) Tj T* (peserta/record) Tj T* ET\nQ\nQ\nq\n1 0 0 1 338.5906 91.3 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Jumlah SKP) Tj T* ET\nQ\nQ\nq\n1 0 0 1 396.4174 86.6 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 11.5 Tm /F2 7.3 Tf 9.4 TL (Record berhasil) Tj T* (diinput) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 66.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (1) Tj T* ET\nQ\nQ\nq\n1 0 0 1 35 66.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama kegiatan]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 179.5669 66.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Tanggal]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 261.4882 66.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 338.5906 66.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 396.4174 66.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (2) Tj T* ET\nQ\nQ\nq\n1 0 0 1 35 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama kegiatan]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 179.5669 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Tanggal]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 261.4882 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 338.5906 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 396.4174 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (3) Tj T* ET\nQ\nQ\nq\n1 0 0 1 35 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama kegiatan]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 179.5669 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Tanggal]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 261.4882 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 338.5906 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 396.4174 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (4) Tj T* ET\nQ\nQ\nq\n1 0 0 1 35 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama kegiatan]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 179.5669 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Tanggal]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 261.4882 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 338.5906 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 396.4174 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 J\n1 j\n.835294 .870588 .909804 RG\n.45 w\nn 0 61.2 m 481.8898 61.2 l S\nn 0 40.8 m 481.8898 40.8 l S\nn 0 20.4 m 481.8898 20.4 l S\nn 0 0 m 481.8898 0 l S\nn 0 0 m 0 110.4 l S\nn 481.8898 0 m 481.8898 110.4 l S\nQ\nQ\nQ\nq\n1 0 0 1 56.69291 459.0898 cm\nQ\nq\n1 0 0 1 56.69291 444.0898 cm\nq\n.086275 .227451 .372549 rg\nBT 1 0 0 1 0 3 Tm /F2 12 Tf 15 TL (5. KENDALA DAN PERMASALAHAN) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 438.0898 cm\nQ\nq\n1 0 0 1 56.69291 357.4898 cm\nq\n.086275 .227451 .372549 rg\nn 0 80.6 481.8898 -19.4 re f*\n.956863 .968627 .980392 rg\nn 0 40.8 481.8898 -20.4 re f*\n0 0 0 rg\nBT /F1 10 Tf 12 TL ET\nq\n1 0 0 1 5 66.2 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (No.) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 66.2 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Kendala / temuan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 139.0158 66.2 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Dampak terhadap pekerjaan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 259.4882 66.2 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Tindak lanjut / PIC) Tj T* ET\nQ\nQ\nq\n1 0 0 1 394.4174 66.2 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Status) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (1) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Uraikan kendala]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 139.0158 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Dampak]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 259.4882 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Tindak lanjut dan PIC]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 394.4174 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Status]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (2) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Uraikan kendala]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 139.0158 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Dampak]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 259.4882 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Tindak lanjut dan PIC]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 394.4174 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Status]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (3) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Uraikan kendala]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 139.0158 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Dampak]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 259.4882 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Tindak lanjut dan PIC]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 394.4174 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Status]) Tj T* ET\nQ\nQ\nq\n1 J\n1 j\n.835294 .870588 .909804 RG\n.45 w\nn 0 40.8 m 481.8898 40.8 l S\nn 0 20.4 m 481.8898 20.4 l S\nn 0 0 m 481.8898 0 l S\nn 0 0 m 0 80.6 l S\nn 481.8898 0 m 481.8898 80.6 l S\nQ\nQ\nQ\nq\n1 0 0 1 56.69291 343.4898 cm\nQ\nq\n1 0 0 1 56.69291 328.4898 cm\nq\n.086275 .227451 .372549 rg\nBT 1 0 0 1 0 3 Tm /F2 12 Tf 15 TL (6. RENCANA TINDAK LANJUT PERIODE BERIKUTNYA) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 322.4898 cm\nQ\nq\n1 0 0 1 56.69291 221.4898 cm\nq\n.086275 .227451 .372549 rg\nn 0 101 481.8898 -19.4 re f*\n.956863 .968627 .980392 rg\nn 0 61.2 481.8898 -20.4 re f*\n.956863 .968627 .980392 rg\nn 0 20.4 481.8898 -20.4 re f*\n0 0 0 rg\nBT /F1 10 Tf 12 TL ET\nq\n1 0 0 1 5 86.6 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (No.) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 86.6 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Rencana pekerjaan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 283.5827 86.6 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Target waktu) Tj T* ET\nQ\nQ\nq\n1 0 0 1 389.5985 86.6 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (PIC) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 66.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (1) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 66.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Rencana tindak lanjut]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 283.5827 66.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Tanggal/periode]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 389.5985 66.7 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (2) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Rencana tindak lanjut]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 283.5827 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Tanggal/periode]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 389.5985 46.3 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (3) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Rencana tindak lanjut]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 283.5827 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Tanggal/periode]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 389.5985 25.9 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (4) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Rencana tindak lanjut]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 283.5827 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Tanggal/periode]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 389.5985 5.5 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama]) Tj T* ET\nQ\nQ\nq\n1 J\n1 j\n.835294 .870588 .909804 RG\n.45 w\nn 0 61.2 m 481.8898 61.2 l S\nn 0 40.8 m 481.8898 40.8 l S\nn 0 20.4 m 481.8898 20.4 l S\nn 0 0 m 481.8898 0 l S\nn 0 0 m 0 101 l S\nn 481.8898 0 m 481.8898 101 l S\nQ\nQ\nQ\nq\n1 0 0 1 56.69291 207.4898 cm\nQ\n \n";
  const STREAM_P3_TEMPLATE = "1 0 0 1 0 0 cm  BT /F1 12 Tf 14.4 TL ET\nq\n.835294 .870588 .909804 RG\n.5 w\nn 56.69291 39.68976 m 538.5827 39.68976 l S\nBT /F1 7 Tf 8.4 TL ET\n.356863 .396078 .45098 rg\nBT 1 0 0 1 56.69291 26.88976 Tm (IMPRUVE.ID | MONTHLY REPORT ONE POGI) Tj T* ET\nBT 1 0 0 1 504.7377 26.88976 Tm (Halaman 3) Tj T* ET\nQ\nq\n1 0 0 1 56.69291 776.8898 cm\nq\n.086275 .227451 .372549 rg\nBT 1 0 0 1 0 3 Tm /F2 12 Tf 15 TL (7. PROJECT PLAN BULAN DEPAN \\(DESEMBER 2026\\)) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 770.8898 cm\nQ\nq\n1 0 0 1 56.69291 758.4898 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 3.7 Tm /F1 8.7 Tf 12.4 TL (Rencana kerja untuk periode bulan depan. Isi periode, target, dan PIC sesuai kesepakatan.) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 750.4898 cm\nQ\nq\n1 0 0 1 56.69291 582.0898 cm\nq\n.086275 .227451 .372549 rg\nn 0 168.4 481.8898 -19.4 re f*\n.956863 .968627 .980392 rg\nn 0 119.2 481.8898 -29.8 re f*\n.956863 .968627 .980392 rg\nn 0 59.6 481.8898 -29.8 re f*\n0 0 0 rg\nBT /F1 10 Tf 12 TL ET\nq\n1 0 0 1 5 154 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (No.) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 154 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Rencana pekerjaan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 225.7559 154 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Periode) Tj T* ET\nQ\nQ\nq\n1 0 0 1 322.1339 154 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (Target) Tj T* ET\nQ\nQ\nq\n1 0 0 1 389.5985 154 cm\nq\n1 1 1 rg\nBT 1 0 0 1 0 2.1 Tm /F2 7.3 Tf 9.4 TL (PIC) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 129.4 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (1) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 129.4 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Pengumpulan / penerimaan data dokter) Tj T* ET\nQ\nQ\nq\n1 0 0 1 225.7559 124.7 cm\nq\nBT 1 0 0 1 0 11.5 Tm 9.4 TL /F1 7.3 Tf .14902 .203922 .270588 rg (Desember 2026,) Tj  T* ([minggu ke-]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 322.1339 129.4 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 389.5985 129.4 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 99.6 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (2) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 99.6 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Pemeriksaan kelengkapan dan kesesuaian data) Tj T* ET\nQ\nQ\nq\n1 0 0 1 225.7559 94.9 cm\nq\nBT 1 0 0 1 0 11.5 Tm 9.4 TL /F1 7.3 Tf .14902 .203922 .270588 rg (Desember 2026,) Tj  T* ([minggu ke-]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 322.1339 99.6 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 389.5985 99.6 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 69.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (3) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 69.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Penginputan data SKP ke sistem ONE POGI) Tj T* ET\nQ\nQ\nq\n1 0 0 1 225.7559 65.1 cm\nq\nBT 1 0 0 1 0 11.5 Tm 9.4 TL /F1 7.3 Tf .14902 .203922 .270588 rg (Desember 2026,) Tj  T* ([minggu ke-]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 322.1339 69.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 389.5985 69.8 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 40 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (4) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 40 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Verifikasi hasil penginputan) Tj T* ET\nQ\nQ\nq\n1 0 0 1 225.7559 35.3 cm\nq\nBT 1 0 0 1 0 11.5 Tm 9.4 TL /F1 7.3 Tf .14902 .203922 .270588 rg (Desember 2026,) Tj  T* ([minggu ke-]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 322.1339 40 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 389.5985 40 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 5 10.2 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (5) Tj T* ET\nQ\nQ\nq\n1 0 0 1 33 10.2 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Monitoring status dan tindak lanjut) Tj T* ET\nQ\nQ\nq\n1 0 0 1 225.7559 5.5 cm\nq\nBT 1 0 0 1 0 11.5 Tm 9.4 TL /F1 7.3 Tf .14902 .203922 .270588 rg (Desember 2026,) Tj  T* ([minggu ke-]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 322.1339 10.2 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([...]) Tj T* ET\nQ\nQ\nq\n1 0 0 1 389.5985 10.2 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL ([Nama]) Tj T* ET\nQ\nQ\nq\n1 J\n1 j\n.835294 .870588 .909804 RG\n.45 w\nn 0 119.2 m 481.8898 119.2 l S\nn 0 89.4 m 481.8898 89.4 l S\nn 0 59.6 m 481.8898 59.6 l S\nn 0 29.8 m 481.8898 29.8 l S\nn 0 0 m 481.8898 0 l S\nn 0 0 m 0 168.4 l S\nn 481.8898 0 m 481.8898 168.4 l S\nQ\nQ\nQ\nq\n1 0 0 1 56.69291 568.0898 cm\nQ\nq\n1 0 0 1 56.69291 553.0898 cm\nq\n.086275 .227451 .372549 rg\nBT 1 0 0 1 0 3 Tm /F2 12 Tf 15 TL (8. DOKUMENTASI / LAMPIRAN) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 547.0898 cm\nQ\nq\n1 0 0 1 56.69291 534.6898 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 3.7 Tm /F1 8.7 Tf 12.4 TL (Centang atau beri keterangan untuk dokumen pendukung yang disertakan:) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 526.6898 cm\nQ\nq\n1 0 0 1 56.69291 460.6898 cm\nq\n.917647 .94902 .972549 rg\nn 0 66 481.8898 -66 re f*\n0 0 0 rg\nBT /F1 10 Tf 12 TL ET\nq\n1 0 0 1 6 50.3 cm\nq\nBT 1 0 0 1 0 2.1 Tm 9.4 TL /F3 7 Tf .356863 .396078 .45098 rg (n) Tj /F1 7.3 Tf .14902 .203922 .270588 rg ( ) Tj ( ) Tj (Rekap data dokter / master tracking) Tj T* ET\nQ\nQ\nq\n1 0 0 1 246.9449 50.3 cm\nq\nBT 1 0 0 1 0 2.1 Tm 9.4 TL /F3 7 Tf .356863 .396078 .45098 rg (n) Tj /F1 7.3 Tf .14902 .203922 .270588 rg ( ) Tj ( ) Tj (Bukti atau tangkapan layar sistem \\(sesuai izin\\)) Tj T* ET\nQ\nQ\nq\n1 0 0 1 6 28.3 cm\nq\nBT 1 0 0 1 0 2.1 Tm 9.4 TL /F3 7 Tf .356863 .396078 .45098 rg (n) Tj /F1 7.3 Tf .14902 .203922 .270588 rg ( ) Tj ( ) Tj (Rekap kegiatan dan keikutsertaan webinar) Tj T* ET\nQ\nQ\nq\n1 0 0 1 246.9449 28.3 cm\nq\nBT 1 0 0 1 0 2.1 Tm 9.4 TL /F3 7 Tf .356863 .396078 .45098 rg (n) Tj /F1 7.3 Tf .14902 .203922 .270588 rg ( ) Tj ( ) Tj (Catatan koordinasi / konfirmasi data) Tj T* ET\nQ\nQ\nq\n1 0 0 1 6 6.3 cm\nq\nBT 1 0 0 1 0 2.1 Tm 9.4 TL /F3 7 Tf .356863 .396078 .45098 rg (n) Tj /F1 7.3 Tf .14902 .203922 .270588 rg ( ) Tj ( ) Tj (Bukti verifikasi atau penyelesaian input) Tj T* ET\nQ\nQ\nq\n1 0 0 1 246.9449 6.3 cm\nq\nBT 1 0 0 1 0 2.1 Tm 9.4 TL /F3 7 Tf .356863 .396078 .45098 rg (n) Tj /F1 7.3 Tf .14902 .203922 .270588 rg ( ) Tj ( ) Tj (Lampiran lain: _______________________) Tj T* ET\nQ\nQ\nq\n1 J\n1 j\n.835294 .870588 .909804 RG\n.45 w\nn 0 66 m 481.8898 66 l S\nn 0 0 m 481.8898 0 l S\nn 0 0 m 0 66 l S\nn 481.8898 0 m 481.8898 66 l S\nn 0 44 m 481.8898 44 l S\nn 0 22 m 481.8898 22 l S\nn 240.9449 0 m 240.9449 66 l S\nQ\nQ\nQ\nq\n1 0 0 1 56.69291 446.6898 cm\nQ\nq\n1 0 0 1 56.69291 431.6898 cm\nq\n.086275 .227451 .372549 rg\nBT 1 0 0 1 0 3 Tm /F2 12 Tf 15 TL (9. PENUTUP) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 425.6898 cm\nQ\nq\n1 0 0 1 56.69291 388.4898 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 28.5 Tm /F1 8.7 Tf 12.4 TL (Demikian laporan bulanan kegiatan pengelolaan dan penginputan data SKP dokter dalam program ONE POGI untuk periode) Tj T* ([Bulan Tahun] ini disusun sebagai dokumentasi pelaksanaan, monitoring progres, dan bahan evaluasi serta koordinasi untuk) Tj T* (periode berikutnya.) Tj T* ET\nQ\nQ\nq\n1 0 0 1 56.69291 376.4898 cm\nQ\nq\n1 0 0 1 56.69291 302.0898 cm\nq\n.917647 .94902 .972549 rg\nn 0 74.4 481.8898 -74.4 re f*\n0 0 0 rg\nBT /F1 10 Tf 12 TL ET\nq\n1 0 0 1 6 59 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Disusun oleh,) Tj T* ET\nQ\nQ\nq\n1 0 0 1 246.9449 59 cm\nq\n.14902 .203922 .270588 rg\nBT 1 0 0 1 0 2.1 Tm /F1 7.3 Tf 9.4 TL (Diperiksa / diketahui oleh,) Tj T* ET\nQ\nQ\nq\n1 0 0 1 6 3 cm\nq\nBT 1 0 0 1 0 39.7 Tm 9.4 TL /F1 7.3 Tf .14902 .203922 .270588 rg  T*  T* ([Nama PIC]) Tj  T* (Impruve.Id) Tj  T* (Tanggal: __________) Tj T* ET\nQ\nQ\nq\n1 0 0 1 246.9449 3 cm\nq\nBT 1 0 0 1 0 39.7 Tm 9.4 TL /F1 7.3 Tf .14902 .203922 .270588 rg  T*  T* ([Nama / Jabatan]) Tj  T* ([POGI / pihak terkait]) Tj  T* (Tanggal: __________) Tj T* ET\nQ\nQ\nq\n1 J\n1 j\n.835294 .870588 .909804 RG\n.45 w\nn 0 74.4 m 481.8898 74.4 l S\nn 0 0 m 481.8898 0 l S\nn 0 0 m 0 74.4 l S\nn 481.8898 0 m 481.8898 74.4 l S\nQ\nQ\nQ\nq\n1 0 0 1 56.69291 288.0898 cm\nQ\nq\n1 0 0 1 56.69291 258.0898 cm\nq\n.356863 .396078 .45098 rg\nBT 1 0 0 1 0 22.7 Tm /F1 7.3 Tf 10 TL (Catatan pengisian: Gunakan satuan hitung yang konsisten. Bedakan dokter unik, record keikutsertaan kegiatan, dan transaksi/input SKP. Status) Tj T* (\\223berhasil\\224 sebaiknya hanya diberikan setelah hasil input dapat diverifikasi pada sistem. Hindari mencantumkan data pribadi sensitif yang tidak) Tj T* (diperlukan dalam laporan.) Tj T* ET\nQ\nQ\n \n";

  const MONTHLY_RECORDS = {
    'November 2026': {
      date: '20 November 2026',
      cutoff: '1 November 2026',
      realSkp: '464,5 SKP',
      doctors: '161 Dokter',
      records: '1.420 Record',
      pending: '551 SKP (19 Dr)',
      kendala: '8 Berkas',
      nextMonth: 'DESEMBER 2026',
      signDate: '20 November 2026'
    },
    'Oktober 2026': {
      date: '31 Oktober 2026',
      cutoff: '1 Oktober 2026',
      realSkp: '493,5 SKP',
      doctors: '158 Dokter',
      records: '1.380 Record',
      pending: '412 SKP (14 Dr)',
      kendala: '5 Berkas',
      nextMonth: 'NOVEMBER 2026',
      signDate: '31 Oktober 2026'
    },
    'September 2026': {
      date: '30 September 2026',
      cutoff: '1 September 2026',
      realSkp: '923,5 SKP',
      doctors: '152 Dokter',
      records: '1.520 Record',
      pending: '320 SKP (9 Dr)',
      kendala: '4 Berkas',
      nextMonth: 'OKTOBER 2026',
      signDate: '30 September 2026'
    },
    'Agustus 2026': {
      date: '31 Agustus 2026',
      cutoff: '1 Agustus 2026',
      realSkp: '257,0 SKP',
      doctors: '149 Dokter',
      records: '1.250 Record',
      pending: '480 SKP (20 Dr)',
      kendala: '6 Berkas',
      nextMonth: 'SEPTEMBER 2026',
      signDate: '31 Agustus 2026'
    },
    'Juli 2026': {
      date: '31 Juli 2026',
      cutoff: '1 Juli 2026',
      realSkp: '214,5 SKP',
      doctors: '146 Dokter',
      records: '1.180 Record',
      pending: '510 SKP (22 Dr)',
      kendala: '7 Berkas',
      nextMonth: 'AGUSTUS 2026',
      signDate: '31 Juli 2026'
    },
    'Juni 2026': {
      date: '30 Juni 2026',
      cutoff: '1 Juni 2026',
      realSkp: '821,5 SKP',
      doctors: '148 Dokter',
      records: '1.490 Record',
      pending: '390 SKP (12 Dr)',
      kendala: '5 Berkas',
      nextMonth: 'JULI 2026',
      signDate: '30 Juni 2026'
    }
  };

  function replaceFirst(source, target, replacement) {
    const idx = source.indexOf(target);
    if (idx === -1) return source;
    return source.substring(0, idx) + replacement + source.substring(idx + target.length);
  }

  function populateStreams(monthName, dynamicOptions = {}) {
    const m = MONTHLY_RECORDS[monthName] || MONTHLY_RECORDS['November 2026'];
    const period = monthName || 'November 2026';

    const picName = dynamicOptions.picName ||
      (typeof window !== 'undefined' && window.CURRENT_USER && window.CURRENT_USER.name) ||
      (typeof window !== 'undefined' && window.AUTH_USER && window.AUTH_USER.name) ||
      'Admin POGI';

    const liveDocs = (dynamicOptions.doctors && dynamicOptions.doctors.length > 0)
      ? dynamicOptions.doctors
      : ((typeof window !== 'undefined' && Array.isArray(window.LIVE_SERVER_DOCTORS) && window.LIVE_SERVER_DOCTORS.length > 0)
        ? window.LIVE_SERVER_DOCTORS
        : []);

    // Helper for doctor sample rows
    function getSampleDoc(idx, fallbackName, fallbackProv) {
      if (liveDocs.length > 0) {
        const d = liveDocs[idx % liveDocs.length];
        return {
          name: d.name || fallbackName,
          province: d.province || fallbackProv,
          skp: (d.q4Credits || d.total_skp || 5) + ' SKP',
          status: (d.status === 'Complete' || d.status === 'COMPLETE') ? 'Terverifikasi' : 'Proses Verifikasi'
        };
      }
      return {
        name: fallbackName,
        province: fallbackProv,
        skp: '5 SKP',
        status: 'Terverifikasi'
      };
    }

    const doc1 = getSampleDoc(0, 'Dokter Spesialis Obgyn I', 'DKI Jakarta');
    const doc2 = getSampleDoc(1, 'Dokter Spesialis Obgyn II', 'Jawa Barat');

    // ================= PAGE 1 =================
    let s1 = STREAM_P1_TEMPLATE;
    s1 = s1.replace('([Bulan Tahun])', '(' + period + ')');
    s1 = s1.replace('([Tanggal])', '(' + m.date + ')');
    s1 = s1.replace('([Nama PIC])', '(' + picName + ')');
    s1 = s1.replace('([Jika relevan])', '(Nasional (38 Wilayah POGI))');

    // 5 Indikator Ringkasan Eksekutif
    s1 = replaceFirst(s1, '([...])', '(' + m.doctors + ')');
    s1 = replaceFirst(s1, '([...])', '(' + m.records + ')');
    s1 = replaceFirst(s1, '([...])', '(' + m.realSkp + ')');
    s1 = replaceFirst(s1, '([...])', '(' + m.pending + ')');
    s1 = replaceFirst(s1, '([...])', '(' + m.kendala + ')');

    // Detail Dokter Page 1 (Sampel 2 Dokter dari Database)
    // Row 1
    s1 = replaceFirst(s1, '([Nama / ID])', '(' + doc1.name + ')');
    s1 = replaceFirst(s1, '([Wilayah])', '(' + doc1.province + ')');
    s1 = replaceFirst(s1, '([Nama kegiatan])', '(PIT POGI 2026)');
    s1 = replaceFirst(s1, '([Tanggal])', '(14 Nov 2026)');
    s1 = replaceFirst(s1, '([...])', '(' + doc1.skp + ')');
    s1 = replaceFirst(s1, '([...])', '(18 Nov 2026)');
    s1 = replaceFirst(s1, '([Status / catatan])', '(' + doc1.status + ')');

    // Row 2
    s1 = replaceFirst(s1, '([Nama / ID])', '(' + doc2.name + ')');
    s1 = replaceFirst(s1, '([Wilayah])', '(' + doc2.province + ')');
    s1 = replaceFirst(s1, '([Nama kegiatan])', '(Simposium Fetomaternal)');
    s1 = replaceFirst(s1, '([Tanggal])', '(16 Nov 2026)');
    s1 = replaceFirst(s1, '([...])', '(' + doc2.skp + ')');
    s1 = replaceFirst(s1, '([...])', '(19 Nov 2026)');
    s1 = replaceFirst(s1, '([Status / catatan])', '(' + doc2.status + ')');

    // ================= PAGE 2 =================
    let s2 = STREAM_P2_TEMPLATE;
    const provDefaults = ['Jawa Timur', 'Sumatera Utara', 'Sulawesi Selatan', 'Bali', 'Banten'];
    const actDefaults = ['Workshop USG Obgyn', 'Webinar Onkologi Ginekologi', 'Pelayanan Bakti Sosial', 'Simposium Infertilitas', 'Kursus Endokrinologi'];

    for (let i = 0; i < 5; i++) {
      const d = getSampleDoc(i + 2, 'Dokter Spesialis Obgyn ' + (i + 3), provDefaults[i]);
      s2 = replaceFirst(s2, '([Nama / ID])', '(' + d.name + ')');
      s2 = replaceFirst(s2, '([Wilayah])', '(' + d.province + ')');
      s2 = replaceFirst(s2, '([Nama kegiatan])', '(' + actDefaults[i] + ')');
      s2 = replaceFirst(s2, '([Tanggal])', '(10 Nov 2026)');
      s2 = replaceFirst(s2, '([...])', '(' + d.skp + ')');
      s2 = replaceFirst(s2, '([...])', '(15 Nov 2026)');
      s2 = replaceFirst(s2, '([Status / catatan])', '(' + d.status + ')');
    }

    // Section 4: Rekap Kegiatan
    const rekapKegiatan = [
      ['PIT POGI Nasional 2026', '14-16 Nov 2026', '142 Dokter', '5 SKP', '142 Record (100%)'],
      ['Webinar Fetomaternal Update', '08 Nov 2026', '98 Dokter', '3 SKP', '98 Record (100%)'],
      ['Bakti Sosial Kesehatan Reproduksi', '04 Nov 2026', '64 Dokter', '4 SKP', '64 Record (100%)'],
      ['Pelayanan Pasien Rujukan RSUP', '01-20 Nov 2026', '161 Dokter', '2.67 SKP', '161 Record (100%)']
    ];

    for (let i = 0; i < rekapKegiatan.length; i++) {
      const r = rekapKegiatan[i];
      s2 = replaceFirst(s2, '([Nama kegiatan])', '(' + r[0] + ')');
      s2 = replaceFirst(s2, '([Tanggal])', '(' + r[1] + ')');
      s2 = replaceFirst(s2, '([...])', '(' + r[2] + ')');
      s2 = replaceFirst(s2, '([...])', '(' + r[3] + ')');
      s2 = replaceFirst(s2, '([...])', '(' + r[4] + ')');
    }

    // Section 5: Kendala
    const kendalaList = [
      ['Sinkronisasi NIK dokter kurasi Kemenkes', 'Pending validasi 19 dokter', 'Koordinasi Ditjen Nakes - PIC POGI', 'Proses'],
      ['Berkas sertifikat webinar belum ada link P2KB', 'Tertunda 8 berkas SKP', 'Konfirmasi panitia cabang - PIC POGI', 'Selesai'],
      ['Perbedaan penulisan nama dokter di STR & SIP', 'Verifikasi manual 3 dokter', 'Pencocokan data KKI - Admin Cabang', 'Selesai']
    ];

    for (let i = 0; i < kendalaList.length; i++) {
      const k = kendalaList[i];
      s2 = replaceFirst(s2, '([Uraikan kendala])', '(' + k[0] + ')');
      s2 = replaceFirst(s2, '([Dampak])', '(' + k[1] + ')');
      s2 = replaceFirst(s2, '([Tindak lanjut dan PIC])', '(' + k[2] + ')');
      s2 = replaceFirst(s2, '([Status])', '(' + k[3] + ')');
    }

    // Section 6: Rencana Tindak Lanjut
    const tindakList = [
      ['Finalisasi kurasi SKP pending bersama Ditjen Nakes', 'Minggu IV', picName],
      ['Sosialisasi upload bukti logbook digital ONE POGI', 'Minggu I', 'Tim IT ONE POGI'],
      ['Rekonsiliasi data SKP triwulan Q4 bersama 38 Cabang', 'Minggu II', 'Admin Data PP POGI'],
      ['Penerbitan rekap laporan final penagihan invoice', 'Akhir Bulan', 'Keuangan Impruve.Id']
    ];

    for (let i = 0; i < tindakList.length; i++) {
      const t = tindakList[i];
      s2 = replaceFirst(s2, '([Rencana tindak lanjut])', '(' + t[0] + ')');
      s2 = replaceFirst(s2, '([Tanggal/periode])', '(' + t[1] + ')');
      s2 = replaceFirst(s2, '([Nama])', '(' + t[2] + ')');
    }

    // ================= PAGE 3 =================
    let s3 = STREAM_P3_TEMPLATE;
    s3 = s3.replace('7. PROJECT PLAN BULAN DEPAN (DESEMBER 2026)', '7. PROJECT PLAN BULAN DEPAN (' + m.nextMonth + ')');
    s3 = s3.replaceAll('Desember 2026,', m.nextMonth.charAt(0) + m.nextMonth.slice(1).toLowerCase() + ',');

    const planList = [
      ['Minggu I-II', '161 Dokter', picName],
      ['Minggu II', '100% Berkas', 'Verifikator'],
      ['Minggu III', 'Target 450 SKP', 'Tim Input'],
      ['Minggu IV', 'Zero Error', 'Supervisor'],
      ['Akhir Bulan', 'Laporan Lengkap', picName]
    ];

    for (let i = 0; i < planList.length; i++) {
      const p = planList[i];
      s3 = replaceFirst(s3, '([minggu ke-])', '(' + p[0] + ')');
      s3 = replaceFirst(s3, '([...])', '(' + p[1] + ')');
      s3 = replaceFirst(s3, '([Nama])', '(' + p[2] + ')');
    }

    s3 = s3.replace('([Bulan Tahun] ini disusun sebagai dokumentasi pelaksanaan, monitoring progres, dan bahan evaluasi serta koordinasi untuk)',
                    '(' + period + ' ini disusun sebagai dokumentasi pelaksanaan, monitoring progres, dan bahan evaluasi serta koordinasi untuk)');

    const authKetumName = dynamicOptions.ketumName || 'Prof. Dr. dr. Yudi M. Hidayat, Sp.OG, Subsp. Onk';
    s3 = s3.replace('([Nama PIC])', '(' + picName + ')');
    s3 = s3.replace('([Nama / Jabatan])', '(' + authKetumName + ')');
    s3 = s3.replace('([POGI / pihak terkait])', '(Pengurus Pusat POGI)');

    return [s1, s2, s3];
  }

  function assemblePdfBytes(s1, s2, s3) {
    // Binary header ReportLab
    const headerPrefix = '%PDF-1.4\n%\x93\x8c\x8b\x9e ReportLab Generated PDF document (opensource)\n';
    
    // We will build the entire binary byte stream using latin1 / charCodeAt(i) & 0xff
    let str = headerPrefix;
    const offsets = {};

    for (let num = 1; num <= 24; num++) {
      if (OBJS_1_TO_24[num]) {
        offsets[num] = str.length;
        str += num + ' 0 obj\n<<' + OBJS_1_TO_24[num] + '>>\nendobj\n';
      }
    }

    // Obj 25 (Page 1 content)
    offsets[25] = str.length;
    str += '25 0 obj\n<< /Length ' + s1.length + ' >>\nstream\n' + s1 + '\nendstream\nendobj\n';

    // Obj 26 (Page 2 content)
    offsets[26] = str.length;
    str += '26 0 obj\n<< /Length ' + s2.length + ' >>\nstream\n' + s2 + '\nendstream\nendobj\n';

    // Obj 27 (Page 3 content)
    offsets[27] = str.length;
    str += '27 0 obj\n<< /Length ' + s3.length + ' >>\nstream\n' + s3 + '\nendstream\nendobj\n';

    // XREF Table
    const startxref = str.length;
    let xref = 'xref\n0 28\n0000000000 65535 f \n';
    for (let i = 1; i <= 27; i++) {
      const off = offsets[i] || 0;
      xref += String(off).padStart(10, '0') + ' 00000 n \n';
    }

    xref += 'trailer\n<<\n';
    xref += '/ID [<78eac16c25a953eacc7100ace8befa3f><78eac16c25a953eacc7100ace8befa3f>]\n';
    xref += '/Info 16 0 R\n/Root 15 0 R\n/Size 28\n>>\n';
    xref += 'startxref\n' + startxref + '\n%%EOF\n';

    str += xref;

    // Convert string to Uint8Array directly
    const bytes = new Uint8Array(str.length);
    for (let i = 0; i < str.length; i++) {
      bytes[i] = str.charCodeAt(i) & 0xff;
    }

    return bytes;
  }

  function generateOnePogiOfficialPdfBlob(monthName, dynamicOptions) {
    const streams = populateStreams(monthName, dynamicOptions);
    const bytes = assemblePdfBytes(streams[0], streams[1], streams[2]);
    return new Blob([bytes], { type: 'application/pdf' });
  }

  function generateOnePogiOfficialPdfBytes(monthName, dynamicOptions) {
    const streams = populateStreams(monthName, dynamicOptions);
    return assemblePdfBytes(streams[0], streams[1], streams[2]);
  }

  global.generateOnePogiOfficialPdfBlob = generateOnePogiOfficialPdfBlob;
  global.generateOnePogiOfficialPdfBytes = generateOnePogiOfficialPdfBytes;

  if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
      populateStreams,
      assemblePdfBytes,
      generateOnePogiOfficialPdfBytes
    };
  }
})(typeof window !== 'undefined' ? window : global);
