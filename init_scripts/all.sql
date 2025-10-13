create table Users
(
    ID              int auto_increment
        primary key,
    Username        text          not null,
    user_email      varchar(255)  not null,
    profile_picture longblob      null,
    HashedPassword  text          not null,
    AccessLevel     int           not null,
    Enabled         tinyint(1)    not null,
    Score           int default 0 not null
);

INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Tester', '', null, '$2y$10$IY.HhoyfwNp8QbIx.YelyO2otFeMu4vjVLOwmLOIVoM0J.ANwMsNm', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Oliver', '', null, '$2y$10$00IJ8x3VLvaJBztpi05iTOoTY4IPZ/gDYGuthw56AfzJ0Bs.33Xd6', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('DisabledTest', '', null, '$2y$10$Zpvt38iUgYypuZ3pGkqBy.nL0ZRwht73OATIwgC8YjAmxHIrS2dae', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('test1', '', null, '$2y$10$Xkf08qDzwW8PGn9s.7iIDeoSnRgcIAMkJhnE2TYHnRL26O20fKou.', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('test2', '', null, '$2y$10$EXcLN0wu158qQRtt//KA3OtIuLUNo02/F.XYFaxLOTwREbsjDcnoe', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('ryan.cather@ed.act.edu.au', 'ryan.cather@ed.act.edu.au', null, '$2y$10$LYpcgqEzulNLIhn/LxDKiuDCmd4KZQclGdfSrtZ42VouqR7FfAk0S', 2, 1, 75);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('sax', '', null, '$2y$10$sGTeppxswv.By0Uee7E03OniHgXPhrmt12DKNcarsw2suMRlKhPv6', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Dylan', '', null, '$2y$10$l.hzBaQ.MQnm2E06u83orO3KrGlki22KbCRZpH6IdZomW.i82715y', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Admintester', '', null, '$2y$10$vksjcXZYce9LAUi./7CCpucBR9rGH06HHqvlGKJ5MRGbMqT/hgqy6', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Cburrows', '', null, '$2y$10$PEJxEKx3Y443NX.2Qp8/F.VkK.iJfY/xngRnlhSYJF2/SqEKQRvyi', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Admin', '', null, '$2y$10$DQdudJ0ngTrM9DlxOl2ipehaQJyqKWSb5DJ6zeMOnPStMlz/hgLt.', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('User', '', null, '$2y$10$v5DY/DQxszHAF1NwWwB9NO0vuIYQ6Hl9x2QT.UKuL3xuLPSubbtQG', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Dylan2', '', null, '$2y$10$fWNhGR9cQ621HRnJZhcQ3OLmad9Cy8kImE9gE4goI/rDWzbfegDua', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('111', '', null, '$2y$10$W9iT7pZkVezdCgTS7aaTq.ls7qTjmPv9pKKzdPUEMz7omEyEKeTD.', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('J0el', '', null, '$2y$10$e6brSyWAR/xWUUjnL.j3z.mQyahGfM84FPU1Cro0wzYPqB7ZxKG.a', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('saxo', '', null, '$2y$10$2Zxz0/inz1oU654aPX6VIOKm.siUZXiYRoHn5h/ns6WxiVYsN98Iu', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('10liter', '', null, '$2y$10$FNz2nh6RiXkYFDXBhtGlC.khv2eWSGUUWJ7QHOjxYXOPTI8ohITcK', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('1234', '', null, '$2y$10$GahotWx253OeBrqSrkOnz.zfwCyF1BAitz2xCCshw6gaUhnpghRhC', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('ihatemylife', '', null, '$2y$10$j/gnckca6Bei2RfbD.p.RORi8JOkvNtsmHZG9iBGdy/4dpdcgkbhK', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('John', '', null, '$2y$10$z8jpokEH6jc9KUBsCi/3h.5YjrGJ3NhBALXjqf6RajGCMJb53X19C', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('joel', '', null, '$2y$10$dnHq/iWYqZXs.841P5srpO4dBUMxq.CiQ31SGOcOo48aUmo9aMeHC', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('123', '', null, '$2y$10$hzJa.d4RAGH4tJ9UY/btru3QCLAaurTGBAygEs.KVktkZscvC..XK', 1, 0, 35);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Ryan', '', null, '$2y$10$v1ytfm4z4COEtIlPAnuehuwiT5kEjQojhcsTNsc9L.SNLI8/kzbn2', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('User21', '', null, '$2y$10$65YkkDHIz8AOkUPiIBNioeIw4JxKSyJiyARyMaWi53z2r0bCSEni.', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('user5', '', null, '$2y$10$DHz8g/e9pSwyRFBir2VAQe9E2sGWEJtLzFgLe/jLdpLL8Bfgl.yui', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('user22', '', null, '$2y$10$DTNG3152M8HTLEJ/KRWEl.CPKaz2dTvVhFiV/48P.XclY5s5ysLaq', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('115', '', null, '$2y$10$6Qq7G3mgfGKocnAs8IgxEePmLmDkqoEjjxQf7lhoVi.C6cPUphRhq', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('User 1', '', null, '$2y$10$a2nImwwya3eByKPSNXBvauN2lManMdsiZYc4z49ZqPwncAgO.pKda', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Cyber city player', '', null, '$2y$10$KNGhVW1ceZNnOYwhE4lrauZ4Vn/RZSZLqzPhJ3cVFzQncn6D/pLu.', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('qwe', '', null, '$2y$10$ghYf4ORuPzXImjf1G85SZeUGp6qRi7d3p/pilm3Ne5MlD1r4mlawO', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('ajaysayer', '', null, '$2y$10$WbunAJfH79uUU68nVa57t.9pzjgRYCBW4TWzjC7rBJId/Xk44d6Fy', 1, 0, 95);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('FakeJon25', '', null, '$2y$10$xBjndd8oV2JcbI339eJDwetexogjFcyJU0/7PsFYYZYg7UM8FVVfK', 1, 0, 1130);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Glovania', '9937412@schoolsnet.act.edu.au', null, '$2y$10$0jreLrJrGnS2ZD3Yade4zOpTMA8ieRMirXwqzLkK3Uj6aRRzDiVlW', 1, 0, 25);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Mcflys', '', null, '$2y$10$44AW1lD7.SmlCMlphkcT8Os5y16XEMO1JR93MHOVKUOAmKAJAP./G', 1, 1, 345);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Mythie2Oscar', '', null, '$2y$10$28cdrcjpXad8Ze0et6OFAeHTRUs1gr/i3bKm/vCohY8tQ87GANKju', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('lily', '', null, '$2y$10$xOsoUxnqxmDfo6wrw0Kv1eITTFfbcjjSSrgDr3sc1K/8e/3vP/4Li', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Nicholas', '', null, '$2y$10$DwrJfifGBoag1hgg7WxAEetxH6BBK0g/H.xY2tTiTsjWaK8lKnNHy', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('rileytraise', '', null, '$2y$10$N1ZsFRDt3SDKow2gxFWt1O8gGxUz5ub0b1WNHUiDvYxZbTRT3.n.G', 1, 0, 270);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Jordan M', '', null, '$2y$10$vVP9o9KJmuRsPC.CWlWrvu/jFqkrWS6QPINuGbrVfm63S5DK095FS', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('ConnorMcHarry', '', null, '$2y$10$xdWeT9MdhMnNYkCz6b8LU.XTZgjqu724Hki8FWtK1kXZnE5Y3Pq.6', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Hayden Ceely', '', null, '$2y$10$paBP/AxwvE.7Q3ivSPTZk.L6iZJ.iENaMIqZiUb/TFcMyMgA3PqJ6', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('haydon', 'test@gmail.com', null, '$2y$10$HDwrD1DcL3SQsz4pfEWaOO825f35ROfv5myUXUoL9ad2BFSfBImuW', 1, 0, 460);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Calwell', '', null, '$2y$10$Mnwyirph1JMKyDAmZuU0A.Ne0EL0CN1pQ4WqncLq4EoAWzciJJk2e', 1, 1, 480);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('haydon123', 'test@gmail.com', null, '$2y$10$jOTd.MtMquvgsjBJhguKIe3Bb2fD1.qwNF7TsDgsM.avK/052XnDq', 1, 1, 312);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Hayden C', '', null, '$2y$10$d1LtBu//55NlVt9p.wSsEOfMcHb/uEpJ28gVcGwdSIALvGN5JhpxC', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('FakeAdmin26', 'Fake@email.com', null, '$2y$10$MBwu3WXJKtIAJl5OVSTvSu4mVtfkY0eWdiuItgNzSgo5ajN7eOJmC', 1, 1, 45);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Nicholas.', '', null, '$2y$10$ML5fj8IEm39LFPhkDuI55e3Pf.sSmZtEP.yce7klXI0SdzsAfrOc2', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('4200184', '', null, '$2y$10$7bmRWLQ1DTYftunGoKRGc.TE7kj6J8E5WSBZLKbJiDPjhakh15yKu', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Hayden', '', null, '$2y$10$zfRAHy7mJUO71sA5icnqueVblSVlgApPGa7jDqkegpYW/tINnw8gG', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('ajaytest', '', null, '$2y$10$/a1.tDj/6/fsrZCa0UVKDeGidkItUJ912hckMMgRpQIDxAmEnQS.q', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('test123', '', null, '$2y$10$vHmpuVpYyDy6XaY9e05NzOEMiO2W2hy.Gt.dYCs2bu9ddTMFaedLK', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('&lt;h1&gt;name&lt;/h1&gt;', '', null, '$2y$10$QJCJs4dwrSzlpYPlTPmlJuzq9oa4aI2sNfVn4Dwo9xVBjQUayHeo2', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('haydon1', '', null, '$2y$10$ke5d9KPyhBcM25gJR/EGAuQEbXjme/bEHSoc0QpyDUNelj7M9U/AS', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('DomPuric', '', null, '$2y$10$5oODmNC8QOXb7zu29Fh/A.80rMhJvEN.gxxnnFmRbEtuEqz8EwYVC', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('lady gaga', '', null, '$2y$10$ZDRr52onESVtKJg8sYxbneqWAZEFt9lNasRANT8xxT96YcA0PLwt6', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Collo', '', null, '$2y$10$FHvy4/Yab6iw47.s8lYajO75YOX796x28qQ0tDNylU7jMAG0cFF76', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Anastasia', '', null, '$2y$10$kCeGhbA7Z8fDHxDG6cKMc.X4u3vw479.vPf4L.cVD6sZ7wQED90Uu', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Alexflis', '', null, '$2y$10$ZcrKSZ29oIpAzUT0ftQPcepHgseALc0IT6Z/TinaFalwYuxx2HKX6', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Sam', '', null, '$2y$10$XZEaMtoi5IYYRsxYnBcE/.VHAB9bXqIdDWuP6U6uWxZji1xvUQyWS', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Finlay', '', null, '$2y$10$kUFgQG/o3wjnR8gHagNCf.QeuT8EPiZqRyavl3MaK0t2/V6a6kae6', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('ibrahim', '', null, '$2y$10$YmGManeLLr/b5j0tHJ/i/OgC2/9InsuICOS9wENXx4/MgCZ2vKVDC', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('binxmybeloved', '', null, '$2y$10$XH6dRwmhCkmtemifRty9TesccdsKkfQf.0IiZWnNFW4nJqeVS.SVe', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('H20990', '', null, '$2y$10$O1UqrN3JfJANv/C0/0qfmOnXHzzqmnwQPV8FfvgBaAs8SrBL7Gqmm', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('0321841', '', null, '$2y$10$g94S.XGfssXYEZSeU/2uPuyJHkoSLfcj5iyZQ9NHyEs5pI5QD9xOq', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('0321856', '', null, '$2y$10$usASdYv1jeceS46AoiXhyOlShnta5eQyK3CUf3gdUdBRzBeilaZmW', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('julian', '', null, '$2y$10$ien3u4xz3WlePXkCv/jXquEXo55h5XykNpuqZ51HGB7QNMUx79BxW', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('AJ', '', null, '$2y$10$fEn72IKNWfXnuFJNqjzkD.m54gUymARDl6stFQBbTh/IEM31a3ovq', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('oofeman', '', null, '$2y$10$zPB9VmR4OFRCgO8UxvuczO2h2fClrVTPsEwSWl5K08Vk8itdfQvla', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('andy', '', null, '$2y$10$elKA5R1aP74ORKeMx.27geQmWwXQ2BO9TpF.QUUitqtv0IXWyG5fi', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Paul', '', null, '$2y$10$kIOEuMWi2/pW9kgqw7RflekETq13P4k5tkntmrV1xhfoZWz4SnECq', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('JaydenMC', '', null, '$2y$10$unp11TL0y6HfxFHhC9W4EOl3j5k8PitE7CmtmcinNpfoFptgUN65G', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Hackzer_will', '', null, '$2y$10$DeE7mUi.ToYaAjdiwJOfKeuX4adCgcyoRzH3/wIhG8l7.fTbFXs7S', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('NadalR7', '', null, '$2y$10$eDC6i15oe4InaUw/jBOexen6bIwOIyAyEm72s6YRDBWXj7zrp2wPC', 1, 0, 15);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Oscar', '', null, '$2y$10$xUqv/.QHseXs/bsNJjD5/uhhs91dcdSma5.nchQaoq2LCxtZY5Ene', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('eddie', '', null, '$2y$10$/9zOruKLXdnLPQQjeXeWuOvagUiIeJC57C7/M2zSYbIiUEkgXHoRm', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Emmet', '', null, '$2y$10$F7HJarpXFb98T89GVRv7r.sERATmgwVqROZ6nt3zji7PGjXdQRPi2', 1, 0, 15);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Balls', '', null, '$2y$10$rbRLzpl2YM6J14xbjqLvOeNC7619DwqKEQJw7mXcH.ek5Gi7O8XdC', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('chrometest', '', null, '$2y$10$wBA5BHnMNr8VQU9H9NDCw.DLRYBbAqDiR44VnE43hTIQA8MJ0AVni', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('ProfileTest1', '', null, '$2y$10$fjS3kJeJ4KLS1C2AZE4Iru9kFxXpbExV8i3UD4Nwix0C.QAHuF3y2', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Kalden2', '', null, '$2y$10$IT.xCR0em5CZW2Dx6wVomOHrj2SiAjXMZKYZa8QTbDWAfiFOwQTaW', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('sigma1', '', null, '$2y$10$xnr4YHWy6hffdePKKgoVW.ueOAaGWrZcd/vm.YwL2jw9C86yCDCty', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('PeetPeet', '', null, '$2y$10$BJEW40ExKYwdf1vM9PQqieXOa5VlyRp3o49HJ2kmJ0OqHz3ol22l2', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('bvb', '', null, '$2y$10$jjlRBHk3ps6IjtTMXvKg1eBegLYaxU0RnGdE4Jp45CGmbpiRU3MDS', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('rjyrjy', '', null, '$2y$10$18vqmbu/4tLr/eqbpEJ5qeCyPggdnMVpQcdVqwUHa/noBF2ZvJLGK', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('new', '', null, '$2y$10$z3abATimtE4rLIdQgiowiek88t6WMioXhdEcPm5/ofKRrowuEHQ6S', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Humuhumunukunukuapua&#039;a', '', null, '$2y$10$i83/9/RomvhCvjlDySHT5OJ9Vf5grVAUSzCgY.rSGHIw/F6DTsdHq', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('King Kong', '', null, '$2y$10$d0VIDVhboY/A0.YVtNWBM.TgOAmSleFoiK0wuoHNqPpU6jfPF3s4m', 1, 0, 115);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Alan Wake', '', null, '$2y$10$f5EbWQkIQLKoyy4XRRMC9.uadabsZ9TBzpLM1L8kfKe21rwSgmgOm', 1, 0, 55);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Alabeasta', '', null, '$2y$10$WeSxoWvIZ8dY97z7C8QF..ZvpK8i4Kj3mv8A/YHz7qlJXD5CAD1Gm', 1, 0, 55);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('his friend', 'abc@gmail.com', null, '$2y$10$X18n2KK9CrcjCqVs56lqhuVWPrSJaBiy0qTfed2sbznkf5CmfBBAe', 1, 0, 1000);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('tjntrjnh', 'cde@gmail.com', null, '$2y$10$Z2P6lDbpMw3s2uAlNTLP3eSQyYD9fkP9DL0zCCCkoZymcumj9c6Ii', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('ytrjhtrjh', 'efg@gmail.com', null, '$2y$10$CbZbDlZiY1BNlwJr7C3EyOGO6LQgk5jJQCskFQAiKCG9q9La.7jwm', 1, 0, 15);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('ytr', 'ytr@gmail.com', null, '$2y$10$JuGXNK.h.HCiJ/o5mlv2IecWBA.A6vINLQrlw9Q4AC/b6ljqxJlsm', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Jedi', '9582398573295@gmail.com', null, '$2y$10$DoazZLgLLq3oRols.sT3BO3Edvo3UPsUebHq.bysU6YzbD0RxU1D.', 1, 1, 25);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('erbeerb', 'Fake@email.com', null, '$2y$10$VHXpNPvAc0IzIeZNwY7PSOlPmjfcShwnSQQlCrYUkjDM6/X7gC8Y2', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('AmongUs', 'sus69@hotmail.com', null, '$2y$10$IqTAK60dYof8rLhCfhAL5eztnveuTbtmPxg6S2K22FkiriYjq0yG.', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('UpdateTestAccount', '17653981368752@ed.act.edu.au', null, '$2y$10$gAs8lOCuGfPEoQqm1a6Y2eFcG6APs/dvlx1zEiqqC4Oyhko1GrEIK', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('btrugh', 'pp@pp.vom', null, '$2y$10$WVWmwVoROXz.KxexOYQhL.Mg3j98HfYXE02xCE.Vr574mWLztkjim', 1, 0, 465);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('wanniassa', 'wanniassa@fakemail.com', null, '$2y$10$3mFfU1CFbznoMo.3qUwU5uFEKaPuDuPIAXT6dbg0ifaKEALbQj3jy', 1, 1, 355);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('mcKillop', 'mcKillop@fakemail.com', null, '$2y$10$Yxb/iXmbC/IsQTAnTfEt.uGdRMRI8nTtCwVU..IacB1USqKmQ1xC6', 1, 1, 405);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('calwell', 'calwell@fakemail.com', null, '$2y$10$FwRinKNyv5eYdKVrCNHoxetjIGzzFEEaTnp5RWXWu0Ohb8/gv4DdC', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('lanyon', 'lanyon@fakemail.com', null, '$2y$10$hZOhO/tI1V0AOYEfadwrgujTo5jezWKWT.6geZnaH7rhqwqAoIgS.', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('namadgi', 'namadgi@fakemail.com', null, '$2y$10$1ehx8Y/5XzDC4l7djg/ZROX7Hbl.jKepX67FSTRIoyhK1pwMAHDPS', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('chisholm', 'chisholm@fakemail.com', null, '$2y$10$aMxgHYwsgOjDMKm2kRluruENIvPj4Q4ZFrI5pqshpKxlR0ldYtrRC', 1, 1, 270);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('test', 'test@test.com', null, '$2y$10$YO3sZTU1Tncwgux3bkPQBepuPPJ24liSuzpqzBgygbfswJ5hcPPl2', 1, 0, 20);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('laptoptest', 'laptoptest@laptoptest.com', null, '$2y$10$/Scee8csZm3NHpj7o2hjE.MIlfeRBd06Ayo3DzwLzMEDwm6uPm.Rm', 1, 0, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('user45', 'test@email.com', null, '$2y$10$6/Y3RrGX8yBBvztdWu7Zc.H3vcUZQJnfL0iUjKRxP/lUUtL41SRd2', 1, 0, 5);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Hacker-X', 'fake@gmail.com', null, '$2y$10$voCjLeEC9SuTNaVe0LKbxuY5qXTt7/gmUibyZpi1ICPDxTJUmUCR6', 1, 1, 5);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('bgt', 'gt4@bnhyt', null, '$2y$10$jMJztZS./hTuf8rAGZZOtecMP.sLBWG0pVtoGrECeK8C.GsBv8tfq', 1, 0, 25);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('LTC', 'LTC@gmail.com', null, '$2y$10$m/qyGet2R29pE0DyBkTjAeOWIF85rT7KAJyEK2uGpWmg9Yi8/xlma', 1, 0, 180);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Alex', '0627778@schoolsnet.act.edu.au', null, '$2y$10$1f37IZ/A8MIrFw74a5eNs.p5/M/Rodz6Q6muj3D8XSOGhW2tiv5BW', 2, 1, 10);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('lachlandr', '0717649@schoolsnet.act.edu.au', null, '$2y$10$WkLkhUlCWe9RIFioJ.nxIuTXvGU3rhUZpRerS/J3cFYXf/cINp/jy', 1, 1, 30);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('PudgyPeter', '1020311@schoolsnet.act.edu.au', null, '$2y$10$Hlml.h3TrgWq.qtH5MpMWee9iIPzS6tSyFN59f4BrQfDxBjd2s4pW', 1, 1, 30);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Doranako', '0321841@schoolsnet.act.edu.au', null, '$2y$10$NBHo4hVBeDpfR9EpKLrwMOQGF8fSSD3oO6dT.FeqHP33BQxCOHLfq', 1, 1, 80);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Aidan geer', '0538180@schoolsnet.act.edu.au', null, '$2y$10$ttA5SfDoAAgx89Eo6eE8sO1sMXWTpJbT5hDT59Ea6EYmRtkGYUxKu', 2, 1, 80);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('JesseUgljesic', 'jesseugljesic@gmail.com', null, '$2y$10$8zBuyhdV1VKhZL8amrylg.ajxVLdps0bSIfCwF2lHyc9CHY7Aikq6', 2, 1, 30);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Nadal', 'nadal.rahman07@gmail.com', null, '$2y$10$LHvehsxwhqsgIE/8SzoI0e/IosYDk8J7TETyy2Wuq6N8hygTwY3hy', 1, 1, 30);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('juju', '0676682@schoolsnet.act.edu.au', null, '$2y$10$nlmiVsLHDM5C5H.HvCmmzuUbKUjiSmFYWfF8Lvr44hcuo3Il5b9Ai', 1, 1, 30);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Ani', '0852401@schoolsnet.act.edu.au', null, '$2y$10$EiaYaQ/GH7s6.VBgTQghgObqWJIr0T7I5QV22kDsjkdjnvSjfAD7a', 1, 1, 45);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('andy12', '0870510@schoolsnet.act.edu.au', null, '$2y$10$KTU.mdyguAihUcpARjPk2OyCEzd9N068.UwYHgnOmDac1Ckbsp0Yu', 2, 1, 30);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('DiaVolentine', '0321856@schoolsnet.act.edu.au', null, '$2y$10$XjcenWeoZUw6Pd2GUspyTuAiWlH14D.0sXrZZlE4TpvQGKTjIq3bG', 1, 1, 30);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Finlay.Mckenzie', '2681543@schoolsnet.act.edu.au', null, '$2y$10$KVMljBFRjdzOflxmS79TuOWUloo3Q6Gq112FEkBz3nLB0mYt1igb2', 2, 1, 105);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('IbrahimI', 'Ibrahimi@Ibrahimi.com', null, '$2y$10$l5r1dCg0oe7geA8umSIHKeQXP3F4VY4hDpjgZkY3USDEYEYKwnvNm', 2, 1, 55);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Tari Ganas', '0676776@schoolsnet.act.edu.au', null, '$2y$10$NQaN8mIuU.DsuY2Aehk.KudQJtuYtNJt0UyuxK4Uz3v4Zuhr1/Vy6', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Tari', '0676776@schoolsnet.act.edu.au', null, '$2y$10$EF7gbsAvIIK53dcs54rWu.qwE.V7sUdw1uG7XeXVtCvnBXabjJ/Z6', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('LandedTitan220', 'Flisalex823@Gmail.com', null, '$2y$10$rQaR1ZoSdtE8rmMZ4g9IY.up3q2AYLIvNXwhWHk.b3f2k/oAYS4/S', 1, 1, 30);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Tashi', '8030071@schoolsnet.act.edu.au', null, '$2y$10$5FvbSwi9VBHUrLO7aYPqg.u3c7kYlgKW9T9weW2NqC7TaWwlYysc2', 2, 1, 354);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Larraine', 'aquino.larraine@gmail.com', null, '$2y$10$YvYPaY.zNCmZxCZnUDoZaOI8Zj.fCp7UnKK.4H2HCYmMJsk5eY87y', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('TippyTetris', 'nadal.rahman07@gmail.com', null, '$2y$10$tUUZlKaMLR/9OIF5iUr8X.jCuJ2dZHcR9j.fWSwgZFxXQk5xy66EW', 2, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('0483182', '0483182@schoolsnet.act.edu.au', null, '$2y$10$A2I6gccTK/LX5aEkdi4Apux0GHmsZ.LcGmNr83mmJXIJnJvO1vEp.', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('testa', 'testa@test.com', null, '$2y$10$QUoMA6Mkq53gjLJABiSHgure.snqAXFtgGVlFfp/vh5COZRFamiBS', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('testa2', 'testa2@test.com', null, '$2y$10$7Im/7LghZaRJlpEGT1LhfuigjzRPLYb4o1hsEIi3F.OpLRbuAWkg2', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Apple', 'apple@apple', null, '$2y$10$xH2NpYeUir7HPqxhVOvhqu4dgAN/YR48ybqADAtOVr/xBPr0EJqt.', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('tester3', 'test@test', null, '$2y$10$dAfzEM59T2ebMyxQaUxoOOgTyMkOG97mYFzanN1/NxImttaHDN0ve', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('testt', 'test@gmail.com', null, '$2y$10$5aNyziuqAIsbgmFy8/uDDOE4tcVKmtEo0gTspZPPMWzYpfnn3jGiq', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Boranako', '0321841@schoolsnet.act.edu.au', null, '$2y$10$BeR9CQ/KA7k9FgcwXgUhXOFHOiEtNbgsIg5rawPqGF9BykVBD3B92', 1, 1, 5);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Jesse', '0592547@schoolsnet.act.edu.au', null, '$2y$10$Q5rD7cy4fkwEydncrpu/0ucGPfleKQgCYlXaHP29BuLfMVlaaxwAm', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('larraine2', 'aquino.larraine@gmail.com', null, '$2y$10$QBoBlIAin8umProqBN38Pe6qr/gBmYdP4vLkmrQn8vEFTvMRFK.b.', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('5', '5@5', null, '$2y$10$0vDRWBEbVOr7dl1zNHiYROGhzwp8itst2GlR4/8hUCYeUMbPg7NNi', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('TestTurbine', 'apple@apple', null, '$2y$10$Y6ddbtsYb7/pPdGbOVD0sOd438KGIw288dkDmy85lGQ2.x74SjQ8a', 1, 1, 5);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('TestCase', 'apple@apple', null, '$2y$10$zHKfWtwmywHm0DrmG0YKx.HuaVSAq6WBHmeKmO8kUvzxw74/LunXG', 1, 1, 15);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('testeddd', 'apple@apple', null, '$2y$10$vB76jh/U3G9u1oaA7wdfh.LiIfxulKzaxD2rcJm/KXXhIZaQn7Hy2', 1, 1, 0);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('testagain', 'apple@apple', null, '$2y$10$RO./sIz8QhyZUXYQU8URfOmagk8eCEyR2IQ0O5Ll6cffN0aAqaw2q', 1, 1, 5);
INSERT INTO CyberCity.Users (Username, user_email, profile_picture, HashedPassword, AccessLevel, Enabled, Score) VALUES ('Brahim', 'brahim@brahim.com', null, '$2y$10$AKLy0R/db4JtvOfVepIk/Oj1x7knKV3zEQS9y2md6v8tlhdpLm25W', 1, 1, 0);

create table Category
(
    CategoryName text not null,
    id           int auto_increment
        primary key,
    projectID    int  null
);

INSERT INTO CyberCity.Category (CategoryName, projectID) VALUES ('Tutorial', 2025);
INSERT INTO CyberCity.Category (CategoryName, projectID) VALUES ('Networking', 2025);
INSERT INTO CyberCity.Category (CategoryName, projectID) VALUES ('Cryptology', 2025);
INSERT INTO CyberCity.Category (CategoryName, projectID) VALUES ('OSINT', 2025);
INSERT INTO CyberCity.Category (CategoryName, projectID) VALUES ('Hex', 2025);
INSERT INTO CyberCity.Category (CategoryName, projectID) VALUES ('Web', 2025);
INSERT INTO CyberCity.Category (CategoryName, projectID) VALUES ('WIP', 2024);


create table ChallengeData
(
    id        int auto_increment
        primary key,
    moduleID  int  null,
    reference text null,
    data      text null
);

INSERT INTO CyberCity.ChallengeData (moduleID, reference, data) VALUES (44, 'Email_1: John.R: \'Don\'t forget to have no repeating charaters Xen\'', 'Email 1');
INSERT INTO CyberCity.ChallengeData (moduleID, reference, data) VALUES (44, 'E_2', 'Email 2');
INSERT INTO CyberCity.ChallengeData (moduleID, reference, data) VALUES (44, 'E_3', 'Email 3');
INSERT INTO CyberCity.ChallengeData (moduleID, reference, data) VALUES (44, 'E_4', 'Email 4');
INSERT INTO CyberCity.ChallengeData (moduleID, reference, data) VALUES (44, 'E_5', 'Email 5');

create table Challenges
(
    ID                int auto_increment
        primary key,
    challengeTitle    text                 null,
    challengeText     text                 null,
    flag              text                 not null,
    pointsValue       int                  not null,
    moduleName        varchar(255)         null,
    moduleValue       varchar(255)         null,
    dockerChallengeID varchar(255)         null,
    container         int                  null,
    Image             text                 null,
    Enabled           tinyint(1) default 1 null,
    categoryID        int                  null,
    files             text                 null,
    constraint xChallenges_Category_id_fk
        foreign key (categoryID) references Category (id)
);

INSERT INTO CyberCity.Challenges (challengeTitle, challengeText, flag, pointsValue, moduleName, moduleValue, dockerChallengeID, container, Image, Enabled, categoryID, files) VALUES ('Traffic Jammed', 'We received a call about the city\'s traffic lights going haywire! This is a nuisance to the city, citizens as well as a major safety hazard. Being the city\'s on-call electrician, we have placed it upon you to rewire the lights. Can you successfully fix the uncoordinated lights and bring peace back to to the road? Your credentials are:', 'CTF{operation_greenwave}', 5, 'TrafficLights', '1', null, 3, 'trafficlights4.gif', 1, 2, null);
INSERT INTO CyberCity.Challenges (challengeTitle, challengeText, flag, pointsValue, moduleName, moduleValue, dockerChallengeID, container, Image, Enabled, categoryID, files) VALUES ('Open Sesame', 'A citizen has made an urgent distress call from their home. Initial scans suggest they\'ve lost the key to their garage and are now trapped inside with their car, unable to get to work. As a part of the emergency response team, you have been assigned the task of remotely opening the locked door, without causing any damage to it or the garage, as per the request of the citizen. Will you be able to get it safely unlocked?', 'CTF{Alohomora}', 5, 'GarageDoor', '0', null, null, 'garagedoor4.gif', 1, 2, null);
INSERT INTO CyberCity.Challenges (challengeTitle, challengeText, flag, pointsValue, moduleName, moduleValue, dockerChallengeID, container, Image, Enabled, categoryID, files) VALUES ('Alarm Anomaly', 'A burglar briefly disarmed the police station’s alarm. The suspect is in custody, but the alarm is still offline. You’ve been called in to bring it back. A suspicious file named Alarm.png was left behind. It looks normal… but is it?

User: RoboCop
Password: TotallySecure01', 'CTF{beep_beep}', 5, 'Alarm', '0', 'alarmAnomaly', 1, 'buzzer.jpg', 1, 3, null);
INSERT INTO CyberCity.Challenges (challengeTitle, challengeText, flag, pointsValue, moduleName, moduleValue, dockerChallengeID, container, Image, Enabled, categoryID, files) VALUES ('Turbine Takeover', 'The city\'s wind turbine has broken down! Being the city\'s main source of power, everyone has entered a state of panic. Fears are growing as the night approaches, threatening to plunge the city into total darkness. As one of the few trained windtechs, and with the clock ticking, you have been assigned to get the turbine operating once again. Can you do it before nightfall arrives? 

While combing through the turbine’s diagnostic logs, your team uncovered a strange, out-of-place file buried deep in an old backup directory. It wasn’t referenced in any current maintenance:', 'CTF{w1ndm1ll_w1nner}', 5, 'Windmill', '1', null, null, 'windmill4.gif', 1, 3, '/CyberCity/website/assets/Files/control_terminal_backup.zip');
INSERT INTO CyberCity.Challenges (challengeTitle, challengeText, flag, pointsValue, moduleName, moduleValue, dockerChallengeID, container, Image, Enabled, categoryID, files) VALUES ('Train Turmoil', 'The CyberCity rail system has gone haywire overnight. A rogue operator locked the train control panel behind a secure container and vanished. The morning commute is in chaos, and the city needs you to get the train back on track.

Your mission: brute force your way into the system, locate the hidden control script, and activate the train. If successful, the train will complete its route and display the flag on the station’s E-Ink board.', 'CTF{Ah_Ch00Ch00}', 5, 'Train', '1', null, null, null, 1, 6, null);
INSERT INTO CyberCity.Challenges (challengeTitle, challengeText, flag, pointsValue, moduleName, moduleValue, dockerChallengeID, container, Image, Enabled, categoryID, files) VALUES ('wee lcd', 'its the lcd', 'CTF{yay_lights}', 5, 'LCD', '0', null, null, null, 1, 6, null);


create table ContactUs
(
    ID       int auto_increment
        primary key,
    Username text       not null,
    Email    text       not null,
    IsRead   tinyint(1) not null
);

INSERT INTO CyberCity.ContactUs (Username, Email, IsRead) VALUES ('Oliver', 'test123@gmail.com', 0);
INSERT INTO CyberCity.ContactUs (Username, Email, IsRead) VALUES ('Oliver', 'teser1@gmail.com', 0);
INSERT INTO CyberCity.ContactUs (Username, Email, IsRead) VALUES ('fef', 'test123', 0);
INSERT INTO CyberCity.ContactUs (Username, Email, IsRead) VALUES ('dewf', 'test12', 0);
INSERT INTO CyberCity.ContactUs (Username, Email, IsRead) VALUES ('agfadfga', 'ryan.cather@ed.act.edu.au', 0);
INSERT INTO CyberCity.ContactUs (Username, Email, IsRead) VALUES ('User21', '27@gmail.com', 1);
INSERT INTO CyberCity.ContactUs (Username, Email, IsRead) VALUES ('saxo', 'test.com', 1);
INSERT INTO CyberCity.ContactUs (Username, Email, IsRead) VALUES ('Oliver', '123@test.com', 1);
INSERT INTO CyberCity.ContactUs (Username, Email, IsRead) VALUES ('no', 'doesthisevenwork@notgmail.com', 1);
INSERT INTO CyberCity.ContactUs (Username, Email, IsRead) VALUES ('Problum chiels', 'tjis page isnt working', 0);


create table DockerContainers
(
    ID              int auto_increment
        primary key,
    timeInitialised timestamp not null,
    userID          int       not null,
    challengeID     text      not null,
    port            int       null
);

create table eventLog
(
    id         int auto_increment
        primary key,
    userName   text     not null,
    eventText  text     not null,
    datePosted datetime not null
);

INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2023-12-01 14:00:02');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.50', '2023-12-01 14:00:07');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Unknown', 'Attempted to access eventLog.php via GET request.', '2023-12-01 14:04:35');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Unknown', 'Attempted to access eventLog.php via GET request.', '2023-12-01 14:04:37');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Unknown', 'Attempted to access eventLog.php via GET request.', '2023-12-01 14:07:43');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Unknown', 'Attempted to access eventLog.php via GET request.', '2023-12-01 14:55:41');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2023-12-04 11:34:03');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.51', '2023-12-04 11:34:04');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2023-12-04 11:55:39');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.51', '2023-12-04 11:55:40');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.51', '2023-12-04 13:30:11');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-02-07 14:40:08');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.50', '2024-02-07 14:40:08');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-03-08 11:23:53');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.7', '2024-03-08 11:24:01');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.7', '2024-03-08 11:35:34');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-03-08 12:02:44');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Unknown', 'Attempted to access eventLog.php via GET request.', '2024-04-16 15:04:35');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Unknown', 'Attempted to access eventLog.php via GET request.', '2024-04-18 10:46:54');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Unknown', 'Attempted to access eventLog.php via GET request.', '2024-04-18 10:51:58');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 09:06:22');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-05-15 09:22:35');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 09:25:14');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 09:34:41');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-05-15 09:34:49');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 09:35:38');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-05-15 09:36:39');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 09:36:45');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-05-15 09:46:11');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 09:46:12');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-05-15 09:50:34');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 09:50:37');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-05-15 10:19:24');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 10:19:25');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-05-16 09:17:55');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-16 09:18:20');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.42', '2024-06-04 10:29:01');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-07-31 14:51:32');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.4', '2024-07-31 14:51:34');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.7', '2024-08-16 11:19:52');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.7', '2024-08-16 11:21:03');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-08-16 11:22:49');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.7', '2024-08-16 11:22:51');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-09-02 09:44:16');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.110', '2024-09-02 09:44:16');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-11-06 13:42:02');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.103', '2024-11-06 13:42:03');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2025-03-24 12:46:25');
INSERT INTO CyberCity.eventLog (userName, eventText, datePosted) VALUES ('Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.108', '2025-03-24 12:46:25');


create table Learn
(
    ID   int auto_increment
        primary key,
    Name text not null,
    Icon text not null,
    Text text not null
);

INSERT INTO CyberCity.Learn (Name, Icon, Text) VALUES ('Inspect Element (Fire Department)', 'FireDept.jpg', '<p> All websites are built with something called HTML, HTML is a markdown language, like all other kinds of markdown/programming languages, HTML has the ability to make comments in the code, these comments are not visible on the end product but is visible in the code. </p> <p> Thankfully all browsers have the ability to see the HTML code that made the website </p> <iframe width="760" height="515" src="https://www.youtube.com/embed/csy5neBsItY?si=sqIKRd6sElKr-eBP" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>');
INSERT INTO CyberCity.Learn (Name, Icon, Text) VALUES ('Caesar Cipher (Windmill)', 'Windmill.jpg', '<p> Cryptography is the art of encrypting data. Encryption is making data not readable unless if the recipient of the data knows hows to unencrypt the data. </p> <p> Ceaser Cipher is a type of encryption named after Julius Caesar, who used it for military messages. </p> <p> Try using the website below to encrypt and even decrypt messages. </p> <iframe src="https://cryptii.com/pipes/caesar-cipher" width="1500" height="515"=></iframe>');
INSERT INTO CyberCity.Learn (Name, Icon, Text) VALUES ('Hex Data (Train Timer)', 'TrainLCD.jpg', '<p> Hex/Hexadecimal is the human friendly version of <a href="https://en.wikipedia.org/wiki/Binary_code" target="_blank"> binary data </a> This data is represented with the symbols of 0-9 (representing data values between 0 to 9) and A-F (representing data values between 10 to 15) </p> <p> While all files have hex values, not all of the hex values in the file may be used by the program using the file. </p> <p> Using the online hex editor below, download and open the image from the challenge and see if you can spot the hidden data </p> <iframe src="https://hexed.it/" width="1500" height="515"=></iframe>');


create table ModuleData
(
    id       int auto_increment
        primary key,
    ModuleID int      null,
    DateTime datetime null,
    Data     text     null,
    constraint ModuleData_RegisteredModules_ID_fk
        foreign key (ModuleID) references archivedRegisteredModules (ID)
);

create table ProjectChallenges
(
    id           int auto_increment
        primary key,
    challenge_id int not null,
    project_id   int not null
);

INSERT INTO CyberCity.ProjectChallenges (challenge_id, project_id) VALUES (1, 2025);
INSERT INTO CyberCity.ProjectChallenges (challenge_id, project_id) VALUES (2, 2024);
INSERT INTO CyberCity.ProjectChallenges (challenge_id, project_id) VALUES (2, 2025);
INSERT INTO CyberCity.ProjectChallenges (challenge_id, project_id) VALUES (3, 2025);
INSERT INTO CyberCity.ProjectChallenges (challenge_id, project_id) VALUES (4, 2025);


create table Projects
(
    project_id   int  not null
        primary key,
    project_name text not null
);

INSERT INTO CyberCity.Projects (project_id, project_name) VALUES (2024, '2024 - Biolab');
INSERT INTO CyberCity.Projects (project_id, project_name) VALUES (2025, '2025 - Nuclear Disaster');

create table UserChallenges
(
    id          int auto_increment
        primary key,
    userID      int null,
    challengeID int null,
    constraint UserChallenges_Users_ID_fk
        foreign key (userID) references Users (ID)
);

create index UserChallenges_Challenges_ID_fk
    on UserChallenges (challengeID);

INSERT INTO CyberCity.UserChallenges (userID, challengeID) VALUES (31, 3);
INSERT INTO CyberCity.UserChallenges (userID, challengeID) VALUES (133, 3);
INSERT INTO CyberCity.UserChallenges (userID, challengeID) VALUES (136, 4);
INSERT INTO CyberCity.UserChallenges (userID, challengeID) VALUES (141, 4);
INSERT INTO CyberCity.UserChallenges (userID, challengeID) VALUES (125, 3);
INSERT INTO CyberCity.UserChallenges (userID, challengeID) VALUES (133, 4);
INSERT INTO CyberCity.UserChallenges (userID, challengeID) VALUES (141, 3);
INSERT INTO CyberCity.UserChallenges (userID, challengeID) VALUES (157, 3);
INSERT INTO CyberCity.UserChallenges (userID, challengeID) VALUES (84, 3);
INSERT INTO CyberCity.UserChallenges (userID, challengeID) VALUES (84, 4);
